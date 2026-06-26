<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\AvailabilityBlock;
use App\Models\Listing;
use App\Models\ListingUnit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CalendarController extends Controller
{
    public function show(Listing $listing): View
    {
        $this->authorize('manageCalendar', $listing);

        $listing->load('units');

        return view('owner.listings.calendar', compact('listing'));
    }

    /**
     * JSON feed of bookings + manual blocks for the given listing, consumed
     * by the calendar JS widget. Optionally scoped to a single unit.
     */
    public function events(Request $request, Listing $listing): JsonResponse
    {
        $this->authorize('manageCalendar', $listing);

        $unitId = $request->integer('unit_id');

        $bookingsQuery = $listing->bookings()
            ->whereIn('status', ['pending', 'confirmed'])
            ->with('user:id,name')
            ->when($unitId, fn ($q) => $q->where('listing_unit_id', $unitId));

        $events = $bookingsQuery->get()->map(fn ($booking) => [
            'id' => 'booking-'.$booking->id,
            'title' => $booking->user->name.' ('.$booking->status.')',
            'start' => $booking->check_in->toDateString(),
            'end' => $booking->check_out
                ? $booking->check_out->addDay()->toDateString()
                : $booking->check_in->addDay()->toDateString(),
            'color' => $booking->status === 'confirmed' ? '#16a34a' : '#f59e0b',
            'type' => 'booking',
        ]);

        $blocksQuery = AvailabilityBlock::whereIn(
            'listing_unit_id',
            $listing->units()->pluck('id')
        )->when($unitId, fn ($q) => $q->where('listing_unit_id', $unitId));

        $blocks = $blocksQuery->get()->map(fn ($block) => [
            'id' => 'block-'.$block->id,
            'title' => $block->reason ?? 'Blocked',
            'start' => $block->start_date->toDateString(),
            'end' => $block->end_date->addDay()->toDateString(),
            'color' => '#6b7280',
            'type' => 'block',
        ]);

        return response()->json($events->concat($blocks)->values());
    }

    public function storeBlock(Request $request, Listing $listing): RedirectResponse
    {
        $this->authorize('manageCalendar', $listing);

        $validated = $request->validate([
            'listing_unit_id' => ['required', 'exists:listing_units,id'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $unit = ListingUnit::findOrFail($validated['listing_unit_id']);
        abort_unless($unit->listing_id === $listing->id, 404);

        AvailabilityBlock::create($validated);

        return back()->with('status', 'Dates blocked.');
    }

    public function destroyBlock(Listing $listing, AvailabilityBlock $block): RedirectResponse
    {
        $this->authorize('manageCalendar', $listing);
        abort_unless($block->listingUnit->listing_id === $listing->id, 404);

        $block->delete();

        return back()->with('status', 'Block removed.');
    }
}

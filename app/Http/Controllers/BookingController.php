<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Listing;
use App\Models\ListingUnit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function create(Listing $listing): View
    {
        abort_unless($listing->isPublished(), 404);

        $listing->load(['units' => fn ($q) => $q->where('is_active', true)]);

        return view('bookings.create', compact('listing'));
    }

    public function store(Request $request, Listing $listing): RedirectResponse
    {
        $this->authorize('create', Booking::class);
        abort_unless($listing->isPublished(), 404);

        $isRestaurant = $listing->isRestaurant();

        $validated = $request->validate([
            'listing_unit_id' => ['required', 'exists:listing_units,id'],
            'check_in' => ['required', 'date', 'after_or_equal:today'],
            'check_out' => [$isRestaurant ? 'nullable' : 'required', 'date', 'after:check_in'],
            'reservation_time' => [$isRestaurant ? 'required' : 'nullable', 'date_format:H:i'],
            'guests' => ['required', 'integer', 'min:1'],
            'special_requests' => ['nullable', 'string', 'max:1000'],
        ]);

        $unit = ListingUnit::where('listing_id', $listing->id)->findOrFail($validated['listing_unit_id']);

        if ($validated['guests'] > $unit->capacity) {
            throw ValidationException::withMessages([
                'guests' => 'This unit can only accommodate up to '.$unit->capacity.' guests.',
            ]);
        }

        $booking = DB::transaction(function () use ($validated, $unit, $listing, $request, $isRestaurant) {
            // Lock the unit's overlapping booking rows for the duration of this transaction
            // to prevent two simultaneous requests from double-booking the same slot.
            DB::table('bookings')
                ->where('listing_unit_id', $unit->id)
                ->whereIn('status', ['pending', 'confirmed'])
                ->lockForUpdate()
                ->get();

            if (! $unit->isAvailable($validated['check_in'], $validated['check_out'] ?? null)) {
                throw ValidationException::withMessages([
                    'check_in' => 'This unit is no longer available for the selected dates.',
                ]);
            }

            $nights = $isRestaurant
                ? 1
                : max(1, \Carbon\Carbon::parse($validated['check_in'])->diffInDays($validated['check_out']));

            $unitPrice = $unit->effectivePrice();
            $totalPrice = $unitPrice * $nights;

            return $listing->bookings()->create([
                'user_id' => $request->user()->id,
                'listing_unit_id' => $unit->id,
                'business_id' => $listing->business_id,
                'check_in' => $validated['check_in'],
                'check_out' => $validated['check_out'] ?? null,
                'reservation_time' => $validated['reservation_time'] ?? null,
                'guests' => $validated['guests'],
                'unit_price' => $unitPrice,
                'quantity' => $nights,
                'total_price' => $totalPrice,
                'currency' => $listing->currency,
                'special_requests' => $validated['special_requests'] ?? null,
            ]);
        });

        ActivityLog::record('booking.created', $booking);

        return to_route('bookings.show', $booking)
            ->with('status', 'Booking request submitted! The owner will confirm shortly.');
    }

    public function index(Request $request): View
    {
        $bookings = $request->user()->bookings()
            ->with(['listing', 'business'])
            ->latest('check_in')
            ->paginate(10);

        return view('bookings.index', compact('bookings'));
    }

    public function show(Booking $booking): View
    {
        $this->authorize('view', $booking);

        $booking->load(['listing', 'listingUnit', 'business', 'user']);

        return view('bookings.show', compact('booking'));
    }

    public function cancel(Booking $booking): RedirectResponse
    {
        $this->authorize('cancel', $booking);

        $booking->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => 'Cancelled by customer',
        ]);

        ActivityLog::record('booking.cancelled', $booking);

        return back()->with('status', 'Booking cancelled.');
    }
}

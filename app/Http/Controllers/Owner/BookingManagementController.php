<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Booking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingManagementController extends Controller
{
    public function index(Request $request): View
    {
        $query = $request->user()->business->bookings()
            ->with(['listing', 'user']);

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $bookings = $query->latest('check_in')->paginate(15)->withQueryString();

        return view('owner.bookings.index', compact('bookings'));
    }

    public function confirm(Booking $booking): RedirectResponse
    {
        $this->authorize('manageStatus', $booking);

        $booking->update(['status' => 'confirmed', 'confirmed_at' => now()]);
        ActivityLog::record('booking.confirmed', $booking);

        return back()->with('status', 'Booking confirmed.');
    }

    public function reject(Request $request, Booking $booking): RedirectResponse
    {
        $this->authorize('manageStatus', $booking);

        $validated = $request->validate([
            'cancellation_reason' => ['required', 'string', 'max:500'],
        ]);

        $booking->update([
            'status' => 'rejected',
            'cancelled_at' => now(),
            'cancellation_reason' => $validated['cancellation_reason'],
        ]);

        ActivityLog::record('booking.rejected', $booking, $validated);

        return back()->with('status', 'Booking rejected.');
    }

    public function complete(Booking $booking): RedirectResponse
    {
        $this->authorize('manageStatus', $booking);

        $booking->update(['status' => 'completed']);
        ActivityLog::record('booking.completed', $booking);

        return back()->with('status', 'Booking marked as completed.');
    }

    public function noShow(Booking $booking): RedirectResponse
    {
        $this->authorize('manageStatus', $booking);

        $booking->update(['status' => 'no_show']);
        ActivityLog::record('booking.no_show', $booking);

        return back()->with('status', 'Booking marked as no-show.');
    }
}

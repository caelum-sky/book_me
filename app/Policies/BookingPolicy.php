<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // scoped per-role in the controller (own bookings vs business bookings vs all)
    }

    public function view(User $user, Booking $booking): bool
    {
        return $user->isSuperAdmin()
            || $booking->user_id === $user->id
            || $booking->business->owner_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isCustomer() && $user->hasVerifiedEmail();
    }

    public function update(User $user, Booking $booking): bool
    {
        // Customers may only update their own pending bookings (e.g. special requests).
        // Owners/admin update status (confirm/reject/complete).
        return $user->isSuperAdmin()
            || $booking->business->owner_id === $user->id
            || ($booking->user_id === $user->id && $booking->status === 'pending');
    }

    public function cancel(User $user, Booking $booking): bool
    {
        return ($booking->user_id === $user->id || $user->isSuperAdmin())
            && $booking->isCancellable();
    }

    public function manageStatus(User $user, Booking $booking): bool
    {
        // Confirm/reject/complete/no-show — business owner of the listing, or super-admin.
        return $user->isSuperAdmin() || $booking->business->owner_id === $user->id;
    }
}
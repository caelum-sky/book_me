<?php

namespace App\Policies;

use App\Models\Listing;
use App\Models\User;

class ListingPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // published listings are public; owners/admin see drafts via scoped queries
    }

    public function view(User $user, Listing $listing): bool
    {
        if ($listing->isPublished()) {
            return true;
        }

        return $user->isSuperAdmin() || $listing->business->owner_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isBusinessOwner() && $user->isApproved() && $user->business?->isApproved();
    }

    public function update(User $user, Listing $listing): bool
    {
        return $user->isSuperAdmin()
            || ($listing->business->owner_id === $user->id && $user->isApproved());
    }

    public function delete(User $user, Listing $listing): bool
    {
        return $user->isSuperAdmin() || $listing->business->owner_id === $user->id;
    }

    public function approve(User $user, Listing $listing): bool
    {
        return $user->isSuperAdmin();
    }

    public function manageCalendar(User $user, Listing $listing): bool
    {
        return $user->isSuperAdmin() || $listing->business->owner_id === $user->id;
    }
}
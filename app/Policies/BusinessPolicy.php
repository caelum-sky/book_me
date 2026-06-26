<?php

namespace App\Policies;

use App\Models\Business;
use App\Models\User;

class BusinessPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isBusinessOwner() || $user->isSuperAdmin();
    }

    public function view(User $user, Business $business): bool
    {
        return $user->isSuperAdmin() || $business->owner_id === $user->id;
    }

    public function create(User $user): bool
    {
        // Any verified business_owner may register one business; super-admin must approve it after.
        return $user->isBusinessOwner() && $user->hasVerifiedEmail() && ! $user->business()->exists();
    }

    public function update(User $user, Business $business): bool
    {
        return $user->isSuperAdmin() || ($business->owner_id === $user->id && $user->isApproved());
    }

    public function delete(User $user, Business $business): bool
    {
        return $user->isSuperAdmin() || $business->owner_id === $user->id;
    }

    public function approve(User $user, Business $business): bool
    {
        return $user->isSuperAdmin();
    }
}
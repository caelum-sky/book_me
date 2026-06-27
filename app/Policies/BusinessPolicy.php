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
        // Super-admin can create a business for any owner at any time.
        // A business owner may only register their own single business,
        // and only if they don't already have one.
        if ($user->isSuperAdmin()) {
            return true;
        }

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
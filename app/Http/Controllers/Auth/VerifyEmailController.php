<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return $this->redirectAfterVerification($user)
                ->with('status', 'already-verified');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return $this->redirectAfterVerification($user)
            ->with('status', 'verified');
    }

    protected function redirectAfterVerification($user): RedirectResponse
    {
        // Business owners still need super-admin approval after verifying email.
        if ($user->isBusinessOwner() && ! $user->isApproved()) {
            return to_route('owner.pending-approval');
        }

        return match (true) {
            $user->isSuperAdmin() => to_route('admin.dashboard'),
            $user->isBusinessOwner() => to_route('owner.dashboard'),
            default => to_route('dashboard'),
        };
    }
}
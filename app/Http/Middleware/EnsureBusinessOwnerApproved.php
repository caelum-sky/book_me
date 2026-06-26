<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureBusinessOwnerApproved
{
    /**
     * Business owners must be approved by a super-admin before they can
     * create/manage listings, view the calendar, or handle bookings.
     * They can still view their pending-approval status page.
     *
     * Also guards against an edge case: an owner whose approval_status is
     * "approved" but who never actually created a business row (e.g. they
     * were approved manually, or a business was deleted after approval).
     * Without this check, every owner-area view that assumes
     * auth()->user()->business exists would throw on a null property access.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->isBusinessOwner()) {
            if (! $user->isApproved()) {
                if ($user->isPendingApproval()) {
                    return redirect()->route('owner.pending-approval');
                }

                // rejected or suspended
                abort(403, 'Your business account is not currently active. Please contact support.');
            }

            if (! $user->business) {
                return redirect()->route('owner.business.create');
            }
        }

        return $next($request);
    }
}
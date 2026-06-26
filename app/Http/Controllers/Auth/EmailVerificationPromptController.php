<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    public function __invoke(Request $request): RedirectResponse|View
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(
                $user->isSuperAdmin() ? route('admin.dashboard')
                    : ($user->isBusinessOwner() ? route('owner.dashboard') : route('dashboard'))
            );
        }

        return view('auth.verify-email');
    }
}
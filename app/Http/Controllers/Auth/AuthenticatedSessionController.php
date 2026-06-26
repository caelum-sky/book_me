<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $request->session()->regenerate();

        $user = Auth::user();

        if (! $user->is_active) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'This account has been deactivated. Please contact support.',
            ]);
        }

        return $this->redirectForRole($user);
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        // Clear any "redirect back here after login" URL that may have been
        // stored in the session (e.g. from hitting a protected page while
        // logged in). Without this, a stale intended-URL can resurface and
        // send the next visitor to a role-specific page instead of home.
        $request->session()->forget('url.intended');

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    protected function redirectForRole($user): RedirectResponse
    {
        if (! $user->hasVerifiedEmail()) {
            return to_route('verification.notice');
        }

        return match (true) {
            $user->isSuperAdmin() => to_route('admin.dashboard'),
            $user->isBusinessOwner() => to_route('owner.dashboard'),
            default => to_route('dashboard'),
        };
    }
}
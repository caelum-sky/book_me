<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', ['user' => $request->user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'phone' => ['nullable', 'string', 'max:30', 'unique:users,phone,'.$user->id],
            'avatar' => ['nullable', 'image', 'max:4096'],
        ]);

        $emailChanged = $validated['email'] !== $user->email;

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
        ]);

        if ($emailChanged) {
            $user->email_verified_at = null;
        }

        $user->save();

        if ($request->hasFile('avatar')) {
            $user->setSingleMedia('avatar', $request->file('avatar'));
        }

        if ($emailChanged) {
            $user->sendEmailVerificationNotification();

            return back()->with('status', 'Profile updated. Please check your email to verify your new address.');
        }

        return back()->with('status', 'Profile updated.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $request->user()->update(['password' => bcrypt($validated['password'])]);

        return back()->with('status', 'Password updated.');
    }

    public function destroyAvatar(Request $request): RedirectResponse
    {
        $request->user()->clearMedia('avatar');

        return back()->with('status', 'Profile photo removed.');
    }
}
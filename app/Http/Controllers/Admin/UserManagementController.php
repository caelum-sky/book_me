<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::query()->with('business')->where('id', '!=', auth()->id());

        if ($role = $request->input('role')) {
            $query->where('role', $role);
        }

        if ($search = $request->input('search')) {
            $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%"));
        }

        $users = $query->latest()->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user): View
    {
        $user->load('business');

        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user): View
    {
        abort_if($user->isSuperAdmin(), 403, 'Cannot edit another super-admin.');

        $user->load('business');

        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        abort_if($user->isSuperAdmin(), 403, 'Cannot edit another super-admin.');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'phone' => ['nullable', 'string', 'max:30', 'unique:users,phone,'.$user->id],
            'role' => ['required', 'in:customer,business_owner'],
            'approval_status' => ['required', 'in:pending,approved,rejected,suspended'],
            'rejection_reason' => ['nullable', 'string', 'max:500'],
            'is_active' => ['boolean'],
        ]);

        $wasApproved = $user->isApproved();

        $validated['is_active'] = $request->boolean('is_active');

        // Clear a stale rejection reason if the admin is no longer rejecting this user.
        if ($validated['approval_status'] !== 'rejected') {
            $validated['rejection_reason'] = null;
        }

        if ($validated['approval_status'] === 'approved' && ! $wasApproved) {
            $validated['approved_by'] = auth()->id();
            $validated['approved_at'] = now();
        }

        $user->update($validated);

        ActivityLog::record('user.updated', $user, ['fields' => array_keys($validated)]);

        return to_route('admin.users.index')->with('status', 'User updated.');
    }

    public function resetPassword(Request $request, User $user): RedirectResponse
    {
        abort_if($user->isSuperAdmin(), 403, 'Cannot reset another super-admin\'s password.');

        $validated = $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user->update(['password' => bcrypt($validated['password'])]);

        ActivityLog::record('user.password_reset_by_admin', $user);

        return back()->with('status', 'Password reset.');
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_if($user->isSuperAdmin(), 403, 'Cannot delete another super-admin.');

        ActivityLog::record('user.deleted', null, ['name' => $user->name, 'email' => $user->email]);

        $user->delete();

        return to_route('admin.users.index')->with('status', 'User deleted.');
    }

    public function toggleActive(User $user): RedirectResponse
    {
        abort_if($user->isSuperAdmin(), 403, 'Cannot deactivate another super-admin.');

        $user->update(['is_active' => ! $user->is_active]);

        ActivityLog::record(
            $user->is_active ? 'user.activated' : 'user.deactivated',
            $user
        );

        return back()->with('status', 'User status updated.');
    }
}
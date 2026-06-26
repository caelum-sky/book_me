<x-dashboard-layout title="Edit User" active="users" subheading="{{ $user->name }}">

    <a href="{{ route('admin.users.index') }}" style="font-size:12.5px;color:var(--text-tertiary);text-decoration:none;">&larr; Back to users</a>

    <div class="bm-form-card" style="margin-top:14px;max-width:560px;">
        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf
            @method('PUT')

            <div class="bm-field">
                <label for="name">Full name</label>
                <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required>
                @error('name') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="bm-field">
                <label for="email">Email address</label>
                <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required>
                @error('email') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="bm-field">
                <label for="phone">Phone</label>
                <input id="phone" name="phone" type="tel" value="{{ old('phone', $user->phone) }}">
                @error('phone') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="bm-field">
                <label for="role">Role</label>
                <select id="role" name="role">
                    <option value="customer" {{ old('role', $user->role) === 'customer' ? 'selected' : '' }}>Customer</option>
                    <option value="business_owner" {{ old('role', $user->role) === 'business_owner' ? 'selected' : '' }}>Business Owner</option>
                </select>
                @error('role') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="bm-field">
                <label for="approval_status">Approval status</label>
                <select id="approval_status" name="approval_status">
                    <option value="pending" {{ old('approval_status', $user->approval_status) === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ old('approval_status', $user->approval_status) === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ old('approval_status', $user->approval_status) === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="suspended" {{ old('approval_status', $user->approval_status) === 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
                <div class="hint" style="margin-top:6px;">For customers this only matters cosmetically — their real gate is email verification. Business owners need this set to Approved to get full dashboard access.</div>
                @error('approval_status') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="bm-field">
                <label for="rejection_reason">Rejection reason <span class="hint">(only used if status is Rejected)</span></label>
                <input id="rejection_reason" name="rejection_reason" type="text" value="{{ old('rejection_reason', $user->rejection_reason) }}">
                @error('rejection_reason') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="bm-field" style="display:flex;align-items:center;gap:8px;">
                <input type="checkbox" name="is_active" id="is_active" value="1" style="width:auto;" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                <label for="is_active" style="margin:0;font-weight:500;">Account active</label>
            </div>

            @if($user->business)
                <div class="bm-field" style="background:var(--panel);border-radius:10px;padding:12px 14px;">
                    <div style="font-size:12.5px;color:var(--text-tertiary);">Business profile</div>
                    <div style="font-size:13.5px;font-weight:600;margin-top:2px;">{{ $user->business->name }}</div>
                    <a href="{{ route('admin.businesses.show', $user->business) }}" style="font-size:12px;color:#a08bff;text-decoration:none;">View business &rarr;</a>
                </div>
            @endif

            <button type="submit" class="bm-btn primary full">Save changes</button>
        </form>
    </div>

    <div class="bm-form-card" style="margin-top:16px;max-width:560px;">
        <h3 style="font-size:14px;font-weight:700;margin:0 0 14px;">Reset password</h3>
        <form method="POST" action="{{ route('admin.users.reset-password', $user) }}">
            @csrf
            <div class="bm-field">
                <label for="password">New password</label>
                <input id="password" name="password" type="password" required>
                @error('password') <div class="error">{{ $message }}</div> @enderror
            </div>
            <div class="bm-field">
                <label for="password_confirmation">Confirm new password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required>
            </div>
            <button type="submit" class="bm-btn secondary full">Reset password</button>
        </form>
    </div>

    <div class="bm-form-card" style="margin-top:16px;max-width:560px;border-color:rgba(248,113,113,0.25);">
        <h3 style="font-size:14px;font-weight:700;margin:0 0 6px;color:#f87171;">Delete account</h3>
        <p style="font-size:12.5px;color:var(--text-tertiary);margin:0 0 14px;">This permanently removes the user and cannot be undone.</p>
        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Permanently delete this user? This cannot be undone.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="bm-btn danger full">Delete user</button>
        </form>
    </div>

</x-dashboard-layout>

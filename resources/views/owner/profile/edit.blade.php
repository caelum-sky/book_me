<x-dashboard-layout title="My Profile" active="profile" subheading="Manage your account details">

    <div class="bm-form-card" style="max-width:560px;">
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px;padding-bottom:20px;border-bottom:1px solid var(--border);">
                <div style="width:72px;height:72px;border-radius:50%;overflow:hidden;background:var(--grad);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    @if($user->avatarUrl())
                        <img id="avatar-preview" src="{{ $user->avatarUrl() }}" style="width:100%;height:100%;object-fit:cover;">
                    @else
                        <span id="avatar-preview-text" style="color:white;font-size:22px;font-weight:700;">
                            {{ collect(explode(' ', $user->name))->map(fn($p) => mb_substr($p,0,1))->take(2)->implode('') }}
                        </span>
                        <img id="avatar-preview" src="" style="width:100%;height:100%;object-fit:cover;display:none;">
                    @endif
                </div>
                <div>
                    <label for="avatar" class="bm-btn secondary sm" style="cursor:pointer;display:inline-block;">Change photo</label>
                    <input id="avatar" name="avatar" type="file" accept="image/*" style="display:none;" onchange="bmPreviewAvatar(this)">
                    @if($user->avatarUrl())
                        <button type="submit" form="remove-avatar-form" class="bm-btn danger sm" style="margin-left:8px;">Remove</button>
                    @endif
                    <div style="font-size:11.5px;color:var(--text-tertiary);margin-top:6px;">JPG or PNG, square works best</div>
                    @error('avatar') <div class="error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="bm-field">
                <label for="name">Full name</label>
                <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required>
                @error('name') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="bm-field">
                <label for="email">Email address</label>
                <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required>
                <div class="hint" style="margin-top:6px;">Changing your email will require you to verify it again.</div>
                @error('email') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="bm-field">
                <label for="phone">Phone number</label>
                <input id="phone" name="phone" type="tel" value="{{ old('phone', $user->phone) }}">
                @error('phone') <div class="error">{{ $message }}</div> @enderror
            </div>

            <button type="submit" class="bm-btn primary full">Save changes</button>
        </form>
    </div>

    <form id="remove-avatar-form" method="POST" action="{{ route('profile.avatar.destroy') }}" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    <div class="bm-form-card" style="max-width:560px;margin-top:16px;">
        <h3 style="font-size:14px;font-weight:700;margin:0 0 16px;">Change password</h3>
        <form method="POST" action="{{ route('profile.password.update') }}">
            @csrf

            <div class="bm-field">
                <label for="current_password">Current password</label>
                <input id="current_password" name="current_password" type="password" required>
                @error('current_password') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="bm-field">
                <label for="password">New password</label>
                <input id="password" name="password" type="password" required>
                @error('password') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="bm-field">
                <label for="password_confirmation">Confirm new password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required>
            </div>

            <button type="submit" class="bm-btn secondary full">Update password</button>
        </form>
    </div>

    <script>
        function bmPreviewAvatar(input) {
            if (!input.files || !input.files[0]) return;
            const reader = new FileReader();
            reader.onload = (e) => {
                const img = document.getElementById('avatar-preview');
                const text = document.getElementById('avatar-preview-text');
                img.src = e.target.result;
                img.style.display = 'block';
                if (text) text.style.display = 'none';
            };
            reader.readAsDataURL(input.files[0]);
        }
    </script>

</x-dashboard-layout>
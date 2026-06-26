<x-auth-layout title="Reset Password">
    <h1>Set a new password</h1>
    <p class="sub">Choose a strong password for your account.</p>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf
        <input type="hidden" name="token" value="{{ request()->route('token') }}">

        <div class="bm-field">
            <label for="email">Email address</label>
            <input id="email" name="email" type="email"
                   value="{{ old('email', request()->query('email')) }}" required autofocus
                   class="{{ $errors->has('email') ? 'has-error' : '' }}">
            @error('email') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="bm-field">
            <label for="password">New password</label>
            <input id="password" name="password" type="password" required
                   class="{{ $errors->has('password') ? 'has-error' : '' }}">
            @error('password') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="bm-field">
            <label for="password_confirmation">Confirm new password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required>
        </div>

        <div class="bm-divider"></div>

        <button type="submit" class="bm-btn primary full">Reset password</button>
    </form>
</x-auth-layout>
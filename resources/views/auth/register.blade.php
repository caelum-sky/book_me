<x-auth-layout title="Sign Up">
    <h1>Create your account</h1>
    <p class="sub">Join BookMe to book stays and dining, or list your business.</p>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="bm-field">
            <label>I am a…</label>
            <div class="bm-role-pick">
                <label>
                    <input type="radio" name="role" value="customer" {{ old('role', 'customer') === 'customer' ? 'checked' : '' }}>
                    <span>Customer</span>
                </label>
                <label>
                    <input type="radio" name="role" value="business_owner" {{ old('role') === 'business_owner' ? 'checked' : '' }}>
                    <span>Business Owner</span>
                </label>
            </div>
            @error('role') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="bm-field">
            <label for="name">Full name</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus
                   class="{{ $errors->has('name') ? 'has-error' : '' }}">
            @error('name') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="bm-field">
            <label for="email">Email address</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required
                   class="{{ $errors->has('email') ? 'has-error' : '' }}">
            @error('email') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="bm-field">
            <label for="phone">Phone number <span class="hint">(optional)</span></label>
            <input id="phone" name="phone" type="tel" value="{{ old('phone') }}"
                   class="{{ $errors->has('phone') ? 'has-error' : '' }}">
            @error('phone') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="bm-field">
            <label for="password">Password</label>
            <input id="password" name="password" type="password" required
                   class="{{ $errors->has('password') ? 'has-error' : '' }}">
            @error('password') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="bm-field">
            <label for="password_confirmation">Confirm password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required>
        </div>

        <div class="bm-divider"></div>

        <button type="submit" class="bm-btn primary full">Create account</button>
    </form>

    <div class="switch">
        Already have an account? <a href="{{ route('login') }}">Log in</a>
    </div>
</x-auth-layout>
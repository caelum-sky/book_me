<x-auth-layout title="Log In">
    <h1>Welcome back</h1>
    <p class="sub">Log in to manage your bookings or business.</p>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="bm-field">
            <label for="email">Email address</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                   class="{{ $errors->has('email') ? 'has-error' : '' }}">
            @error('email') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="bm-field">
            <label for="password" style="display:flex;justify-content:space-between;align-items:baseline;">
                <span>Password</span>
                <a href="{{ route('password.request') }}" class="bm-forgot">Forgot password?</a>
            </label>
            <input id="password" name="password" type="password" required
                   class="{{ $errors->has('password') ? 'has-error' : '' }}">
            @error('password') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="bm-field bm-check-row">
            <input type="checkbox" name="remember" id="remember">
            <label for="remember">Remember me</label>
        </div>

        <div class="bm-divider"></div>

        <button type="submit" class="bm-btn primary full">Log in</button>
    </form>

    <div class="switch">
        Don't have an account? <a href="{{ route('register') }}">Sign up</a>
    </div>
</x-auth-layout>
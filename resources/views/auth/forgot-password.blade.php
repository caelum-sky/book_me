<x-auth-layout title="Forgot Password">
    <h1>Reset your password</h1>
    <p class="sub">Enter your email and we'll send you a reset link.</p>

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="bm-field">
            <label for="email">Email address</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                   class="{{ $errors->has('email') ? 'has-error' : '' }}">
            @error('email') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="bm-divider"></div>

        <button type="submit" class="bm-btn primary full">Send reset link</button>
    </form>

    <div class="switch">
        <a href="{{ route('login') }}">← Back to log in</a>
    </div>
</x-auth-layout>
<x-auth-layout title="Verify Email">
    <div class="bm-auth-icon-circle" style="background:rgba(124,92,255,0.14);color:#a78bfa;">
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
        </svg>
    </div>

    <h1 style="text-align:center;">Check your email</h1>
    <p class="sub" style="text-align:center;">
        We sent a verification link to
        <strong style="color:var(--bm-text);">{{ auth()->user()->email }}</strong>.
        Click the link in that email to verify your account.
    </p>

    @if (session('status') === 'verification-link-sent')
        <div class="bm-alert success" style="margin-bottom:18px;">
            A new verification link has been sent to your email address.
        </div>
    @endif

    <div class="bm-divider"></div>

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit" class="bm-btn primary full">Resend verification email</button>
    </form>

    <form method="POST" action="{{ route('logout') }}" style="margin-top:10px;">
        @csrf
        <button type="submit" class="bm-btn secondary full">Log out</button>
    </form>
</x-auth-layout>
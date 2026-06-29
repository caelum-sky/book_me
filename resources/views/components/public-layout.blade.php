{{--
    resources/views/components/public-layout.blade.php
    Full-width shell for pages visible to guests (browse, listing detail).
    Same visual system as dashboard-layout but with a simple top nav bar
    instead of a sidebar, since guests have no account/role context.
--}}
@props(['title' => 'BookMe'])
@php
    $user = auth()->user();
    $dashboardRoute = null;

    if ($user) {
        $dashboardRoute = $user->isSuperAdmin()
            ? route('admin.dashboard')
            : ($user->isBusinessOwner()
                ? ($user->isApproved() ? route('owner.dashboard') : route('owner.pending-approval'))
                : route('dashboard'));
    }
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} - BookMe</title>
    @vite(['resources/css/app.css', 'resources/css/dashboard.css', 'resources/js/app.js'])
</head>
<body class="bm-body">
<div class="bm-page">
    <div class="bm-shell bm-public-shell">

        <div class="bm-public-nav">
            <a href="{{ route('home') }}" class="bm-brand-link">
                <span class="bm-logo">B</span>
                <span>BookMe</span>
            </a>

            <div class="bm-public-actions">
                @auth
                    <a href="{{ $dashboardRoute }}" class="bm-btn secondary sm">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="bm-btn secondary sm">Log in</a>
                    <a href="{{ route('register') }}" class="bm-btn primary sm">Sign up</a>
                @endauth
            </div>
        </div>

        @if(session('status'))
            <div class="bm-alert success">
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="bm-alert error">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{ $slot }}

        <div class="bm-footer-bar">
            <span>&copy; {{ date('Y') }} BookMe. All rights reserved.</span>
            <span>Dorms &middot; Houses &middot; Pads &middot; Hotels &middot; Inns &middot; Motels &middot; Restaurants</span>
        </div>
    </div>
</div>
</body>
</html>

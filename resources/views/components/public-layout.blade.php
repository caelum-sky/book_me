{{--
    resources/views/components/public-layout.blade.php
    Full-width shell for pages visible to guests (browse, listing detail).
    Same visual system as dashboard-layout but with a simple top nav bar
    instead of a sidebar, since guests have no account/role context.
--}}
@props(['title' => 'BookMe'])
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} — BookMe</title>
    @vite(['resources/css/app.css', 'resources/css/dashboard.css', 'resources/js/app.js'])
    <style>
/* Add this near the existing .bm-auth-logo rule in resources/css/dashboard.css */
    .bm-auth-logo {
    margin-left: auto;
    margin-right: auto;
    }
    </style>
</head>
<body class="bm-body">
<div class="bm-page">
    <div class="bm-shell" style="grid-template-columns: 1fr; max-width:1240px;">

        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
            <a href="{{ route('home') }}" style="display:flex;align-items:center;gap:10px;text-decoration:none;">
                <span class="bm-logo">B</span>
                <span style="font-size:16px;font-weight:700;color:var(--text-primary);">BookMe</span>
            </a>

            <div style="display:flex;align-items:center;gap:10px;">
                @auth
                    <a href="{{ auth()->user()->isSuperAdmin() ? route('admin.dashboard') : (auth()->user()->isBusinessOwner() ? route('owner.dashboard') : route('dashboard')) }}" class="bm-btn secondary sm">
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
            <span>Dorms · Houses · Pads · Hotels · Inns · Motels · Restaurants</span>
        </div>
    </div>
</div>
</body>
</html>
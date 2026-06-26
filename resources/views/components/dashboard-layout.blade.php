{{--
    resources/views/components/dashboard-layout.blade.php

    Shared shell for every logged-in page (customer, owner, admin).
    Usage:
        <x-dashboard-layout title="My Bookings" active="bookings">
            ... page content ...
        </x-dashboard-layout>

    Optional slots:
        rail        — right-hand 320px rail (omit to get a 2-column shell)
    Props:
        title       — <title> tag + topbar h1 (defaults to page heading below)
        heading     — topbar h1 text (falls back to $title)
        subheading  — topbar sub text
--}}
@props(['title' => 'BookMe', 'heading' => null, 'subheading' => null, 'active' => null])

@php
    $user = auth()->user();
    $role = $user->role ?? 'customer';

    // Per-role nav icon sets: [key => [route name or '#', label, svg path]]
    $navByRole = [
        'customer' => [
            ['key' => 'dashboard', 'route' => 'dashboard', 'label' => 'Overview',
                'svg' => '<path d="M3 11.5 12 4l9 7.5"/><path d="M5 10v10h14V10"/>'],
            ['key' => 'browse', 'route' => 'listings.index', 'label' => 'Browse',
                'svg' => '<circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/>'],
            ['key' => 'bookings', 'route' => 'bookings.index', 'label' => 'My Bookings',
                'svg' => '<rect x="3" y="4" width="18" height="18" rx="3"/><path d="M3 9h18M8 2v4M16 2v4"/>'],
        ],
        'business_owner' => [
            ['key' => 'dashboard', 'route' => 'owner.dashboard', 'label' => 'Overview',
                'svg' => '<path d="M3 11.5 12 4l9 7.5"/><path d="M5 10v10h14V10"/>'],
            ['key' => 'listings', 'route' => 'owner.listings.index', 'label' => 'Listings',
                'svg' => '<path d="M3 9.5 12 3l9 6.5V20a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1Z"/>'],
            ['key' => 'bookings', 'route' => 'owner.bookings.index', 'label' => 'Bookings',
                'svg' => '<rect x="3" y="4" width="18" height="18" rx="3"/><path d="M3 9h18M8 2v4M16 2v4"/>',
                'badge' => $user && $user->business ? ($user->business->bookings()->where('status', 'pending')->count() ?: null) : null],
            ['key' => 'business', 'route' => 'owner.business.edit', 'label' => 'Business',
                'svg' => '<circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 3.6-7 8-7s8 3 8 7"/>'],
        ],
        'super_admin' => [
            ['key' => 'dashboard', 'route' => 'admin.dashboard', 'label' => 'Overview',
                'svg' => '<path d="M3 11.5 12 4l9 7.5"/><path d="M5 10v10h14V10"/>'],
            ['key' => 'businesses', 'route' => 'admin.businesses.index', 'label' => 'Businesses',
                'svg' => '<path d="M3 9.5 12 3l9 6.5V20a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1Z"/>',
                'badge' => \App\Models\Business::where('status', 'pending')->count() ?: null],
            ['key' => 'listings', 'route' => 'admin.listings.index', 'label' => 'Listings',
                'svg' => '<rect x="3" y="4" width="18" height="18" rx="3"/><path d="M3 9h18M8 2v4M16 2v4"/>',
                'badge' => \App\Models\Listing::where('status', 'pending_review')->count() ?: null],
            ['key' => 'users', 'route' => 'admin.users.index', 'label' => 'Users',
                'svg' => '<circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 3.6-7 8-7s8 3 8 7"/>'],
        ],
    ];

    $navItems = $navByRole[$role] ?? $navByRole['customer'];
    $heading = $heading ?? $title;

    $initials = $user
        ? collect(explode(' ', $user->name))->map(fn ($p) => mb_substr($p, 0, 1))->take(2)->implode('')
        : '?';

    $roleLabels = [
        'customer' => 'Customer',
        'business_owner' => 'Business Owner',
        'super_admin' => 'Super Admin',
    ];
    $roleLabel = $roleLabels[$role] ?? 'Account';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} — BookMe</title>
    @vite(['resources/css/app.css', 'resources/css/dashboard.css', 'resources/js/app.js', 'resources/js/bm-modal.js'])
</head>
<body class="bm-body">
<div class="bm-page">
    <div class="bm-shell" style="grid-template-columns: 220px 1fr {{ isset($rail) ? '320px' : '' }};">

        <div class="bm-sidebar">
            <a href="{{ route('home') }}" class="bm-logo-row">
                <span class="bm-logo">B</span>
                <span>
                    <span class="brand" style="display:block;">BookMe</span>
                    <span class="role-tag" style="display:block;">{{ $roleLabel }}</span>
                </span>
            </a>

            <div class="bm-nav-icons">
                @foreach($navItems as $item)
                    <a href="{{ route($item['route']) }}"
                       class="bm-nav-icon {{ $active === $item['key'] ? 'active' : '' }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">{!! $item['svg'] !!}</svg>
                        <span class="label">{{ $item['label'] }}</span>
                        @if(!empty($item['badge']))
                            <span class="badge-count">{{ $item['badge'] }}</span>
                        @endif
                    </a>
                @endforeach
            </div>

            <div class="bm-nav-spacer"></div>

            <div class="bm-nav-bottom">
                <a href="{{ route('profile.edit') }}" class="bm-nav-icon {{ $active === 'profile' ? 'active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 3.6-7 8-7s8 3 8 7"/></svg>
                    <span class="label">Profile</span>
                </a>
                @if($role === 'business_owner')
                    <a href="{{ route('owner.business.edit') }}" class="bm-nav-icon {{ $active === 'settings' ? 'active' : '' }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .34 1.87l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.7 1.7 0 0 0-1.87-.34 1.7 1.7 0 0 0-1.03 1.55V21a2 2 0 1 1-4 0v-.09A1.7 1.7 0 0 0 9 19.36a1.7 1.7 0 0 0-1.87.34l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.7 1.7 0 0 0 4.64 15a1.7 1.7 0 0 0-1.55-1.03H3a2 2 0 1 1 0-4h.09A1.7 1.7 0 0 0 4.64 9a1.7 1.7 0 0 0-.34-1.87l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.7 1.7 0 0 0 9 4.64a1.7 1.7 0 0 0 1.03-1.55V3a2 2 0 1 1 4 0v.09a1.7 1.7 0 0 0 1.03 1.55 1.7 1.7 0 0 0 1.87-.34l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06A1.7 1.7 0 0 0 19.36 9a1.7 1.7 0 0 0 1.55 1.03H21a2 2 0 1 1 0 4h-.09a1.7 1.7 0 0 0-1.51 1.03Z"/></svg>
                        <span class="label">Settings</span>
                    </a>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="bm-nav-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/></svg>
                        <span class="label">Log out</span>
                    </button>
                </form>
            </div>
        </div>


        <div class="bm-main">

            <div class="bm-topbar">
                <div>
                    <h1>{{ $heading }}</h1>
                    @if($subheading)
                        <div class="sub">{{ $subheading }}</div>
                    @endif
                </div>

                @isset($search)
                    <div class="bm-search">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
                        {{ $search }}
                    </div>
                @endisset

                <div class="bm-topbar-actions">
                    <div class="bm-avatar" title="{{ $user->name ?? '' }}" style="overflow:hidden;">
                    @if($user && $user->avatarUrl())
                        <img src="{{ $user->avatarUrl() }}" style="width:100%;height:100%;object-fit:cover;">
                    @else
                        {{ $initials }}
                    @endif
                </div>
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

        @isset($rail)
            <div class="bm-rail">
                {{ $rail }}
            </div>
        @endisset

    </div>
</div>
</body>
</html>
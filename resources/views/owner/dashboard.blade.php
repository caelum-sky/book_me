@php
    $business = auth()->user()->business;
@endphp

<x-dashboard-layout title="Owner Dashboard" active="dashboard" subheading="{{ $business->name ?? 'Set up your business to get started' }}">

    @if(!$business)
        <div class="bm-list-card">
            <div class="bm-empty">
                <div class="icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin:0 auto;"><path d="M3 9.5 12 3l9 6.5V20a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1Z"/></svg>
                </div>
                <div style="margin-bottom:14px;">You haven't set up a business profile yet.</div>
                <a href="{{ route('owner.business.create') }}" class="bm-btn primary">Set up business profile</a>
            </div>
        </div>
    @else
        <div class="bm-stat-row cols-3">
            <a href="{{ route('owner.listings.index') }}" class="bm-stat-card">
                <div class="bm-stat-top">
                    <div class="bm-stat-icon purple">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9.5 12 3l9 6.5V20a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1Z"/></svg>
                    </div>
                    <div class="bm-stat-title">Listings</div>
                </div>
                <div class="bm-stat-value">{{ $business->listings()->count() }}</div>
                <div class="bm-stat-delta"><span class="muted">manage your dorms, hotels, dining &amp; more</span></div>
            </a>

            <a href="{{ route('owner.bookings.index') }}" class="bm-stat-card">
                <div class="bm-stat-top">
                    <div class="bm-stat-icon amber">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="3"/><path d="M3 9h18M8 2v4M16 2v4"/></svg>
                    </div>
                    <div class="bm-stat-title">Pending bookings</div>
                </div>
                <div class="bm-stat-value">{{ $business->bookings()->where('status', 'pending')->count() }}</div>
                <div class="bm-stat-delta"><span class="muted">awaiting your confirmation</span></div>
            </a>

            <a href="{{ route('owner.business.edit') }}" class="bm-stat-card">
                <div class="bm-stat-top">
                    <div class="bm-stat-icon blue">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .34 1.87l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.7 1.7 0 0 0-1.87-.34 1.7 1.7 0 0 0-1.03 1.55V21a2 2 0 1 1-4 0v-.09A1.7 1.7 0 0 0 9 19.36a1.7 1.7 0 0 0-1.87.34l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.7 1.7 0 0 0 4.64 15a1.7 1.7 0 0 0-1.55-1.03H3a2 2 0 1 1 0-4h.09A1.7 1.7 0 0 0 4.64 9a1.7 1.7 0 0 0-.34-1.87l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.7 1.7 0 0 0 9 4.64a1.7 1.7 0 0 0 1.03-1.55V3a2 2 0 1 1 4 0v.09a1.7 1.7 0 0 0 1.03 1.55 1.7 1.7 0 0 0 1.87-.34l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06A1.7 1.7 0 0 0 19.36 9a1.7 1.7 0 0 0 1.55 1.03H21a2 2 0 1 1 0 4h-.09a1.7 1.7 0 0 0-1.51 1.03Z"/></svg>
                    </div>
                    <div class="bm-stat-title">Settings</div>
                </div>
                <div class="bm-stat-value" style="font-size:17px;">Business profile</div>
                <div class="bm-stat-delta"><span class="muted">edit details &amp; storefront</span></div>
            </a>
        </div>

        <div class="bm-list-card">
            <div class="bm-list-head">
                <h2>Quick actions</h2>
            </div>
            <div style="display:flex;gap:10px;flex-wrap:wrap;">
                <a href="{{ route('owner.listings.create') }}" class="bm-btn primary">+ New listing</a>
                <a href="{{ route('owner.listings.index') }}" class="bm-btn secondary">Manage listings</a>
                <a href="{{ route('owner.bookings.index') }}" class="bm-btn secondary">View bookings</a>
            </div>
        </div>
    @endif

</x-dashboard-layout>
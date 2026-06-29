@php
    $firstName = explode(' ', auth()->user()->name)[0];
@endphp

<x-dashboard-layout
    title="Dashboard"
    active="dashboard"
    :heading="'Welcome back, '.$firstName"
    subheading="Your bookings, activity, and quick paths through BookMe."
>
    <div class="bm-stat-row cols-3">
        <div class="bm-stat-card">
            <div class="bm-stat-top">
                <div class="bm-stat-icon blue">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="3"/><path d="M3 9h18M8 2v4M16 2v4"/></svg>
                </div>
                <div class="bm-stat-title">Total bookings</div>
            </div>
            <div class="bm-stat-value">{{ $totalBookings }}</div>
            <div class="bm-stat-delta"><span class="muted">all time</span></div>
        </div>

        <div class="bm-stat-card">
            <div class="bm-stat-top">
                <div class="bm-stat-icon amber">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 8v4l3 3"/><circle cx="12" cy="12" r="9"/></svg>
                </div>
                <div class="bm-stat-title">Upcoming</div>
            </div>
            <div class="bm-stat-value">{{ $upcomingCount }}</div>
            <div class="bm-stat-delta"><span class="muted">confirmed or pending</span></div>
        </div>

        <div class="bm-stat-card">
            <div class="bm-stat-top">
                <div class="bm-stat-icon green">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7H14a3.5 3.5 0 0 1 0 7H6"/></svg>
                </div>
                <div class="bm-stat-title">Total spent</div>
            </div>
            <div class="bm-stat-value">&#8369;{{ number_format($totalSpent, 0) }}</div>
            <div class="bm-stat-delta"><span class="muted">completed bookings</span></div>
        </div>
    </div>

    <div class="bm-list-card">
        <div class="bm-list-head">
            <h2>Quick actions</h2>
        </div>
        <div class="bm-action-grid">
            <a href="{{ route('listings.index') }}" class="bm-action-card">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
                <span>Browse listings</span>
            </a>
            <a href="{{ route('bookings.index') }}" class="bm-action-card">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="18" rx="3"/><path d="M3 9h18M8 2v4M16 2v4"/></svg>
                <span>My bookings</span>
            </a>
            <a href="{{ route('bookings.calendar') }}" class="bm-action-card">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                <span>Calendar</span>
            </a>
            <a href="{{ route('bookings.history') }}" class="bm-action-card">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3.05 11a9 9 0 1 0 .5-4.5"/><path d="M3 3v4h4"/><path d="M12 8v5l3 2"/></svg>
                <span>History</span>
            </a>
            <a href="{{ route('profile.edit') }}" class="bm-action-card">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 3.6-7 8-7s8 3 8 7"/></svg>
                <span>Edit profile</span>
            </a>
        </div>
    </div>

    <div class="bm-list-card">
        <div class="bm-list-head">
            <h2>Upcoming bookings</h2>
            <a href="{{ route('bookings.index') }}">View all &rarr;</a>
        </div>

        @forelse($upcomingBookings as $booking)
            <a href="{{ route('bookings.show', $booking) }}" class="bm-list-row">
                <div class="bm-row-info">
                    <div class="name">{{ $booking->listing->title }}</div>
                    <div class="meta">
                        {{ $booking->business->name }} &middot;
                        {{ $booking->check_in->format('M j, Y') }}
                        @if($booking->check_out) - {{ $booking->check_out->format('M j, Y') }} @endif
                        &middot; &#8369;{{ number_format($booking->total_price, 0) }}
                    </div>
                </div>
                @php
                    $badge = match($booking->status) {
                        'confirmed' => 'badge-green',
                        'pending' => 'badge-amber',
                        'completed' => 'badge-gray',
                        default => 'badge-red',
                    };
                @endphp
                <span class="bm-row-badge {{ $badge }}">{{ ucfirst(str_replace('_', ' ', $booking->status)) }}</span>
            </a>
        @empty
            <div class="bm-empty">
                <div class="icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                </div>
                No upcoming bookings yet.
                <div style="margin-top:10px;"><a href="{{ route('listings.index') }}" class="bm-btn primary sm">Browse listings</a></div>
            </div>
        @endforelse
    </div>

    @if($recentBookings->isNotEmpty())
        <div class="bm-list-card">
            <div class="bm-list-head">
                <h2>Recent activity</h2>
                <a href="{{ route('bookings.history') }}">Full history &rarr;</a>
            </div>

            @foreach($recentBookings as $booking)
                <a href="{{ route('bookings.show', $booking) }}" class="bm-list-row">
                    <div class="bm-row-info">
                        <div class="name">{{ $booking->listing->title }}</div>
                        <div class="meta">{{ $booking->check_in->format('M j, Y') }} &middot; &#8369;{{ number_format($booking->total_price, 0) }}</div>
                    </div>
                    @php
                        $badge = match($booking->status) {
                            'confirmed' => 'badge-green',
                            'pending' => 'badge-amber',
                            'completed' => 'badge-gray',
                            default => 'badge-red',
                        };
                    @endphp
                    <span class="bm-row-badge {{ $badge }}">{{ ucfirst(str_replace('_', ' ', $booking->status)) }}</span>
                </a>
            @endforeach
        </div>
    @endif
</x-dashboard-layout>

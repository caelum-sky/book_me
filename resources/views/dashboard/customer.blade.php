<x-layout title="Dashboard">

<style>
.dash-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 14px; margin-bottom: 28px; }
.dash-stat {
    background: var(--panel-2); border: 1px solid var(--border);
    border-radius: var(--radius-lg); padding: 20px 22px;
}
.dash-stat .label { font-size: 12px; color: var(--text-tertiary); font-weight: 500; margin-bottom: 8px; text-transform: uppercase; letter-spacing: .4px; }
.dash-stat .value { font-size: 28px; font-weight: 700; color: var(--text-primary); }
.dash-stat .sub   { font-size: 12px; color: var(--text-tertiary); margin-top: 4px; }
.dash-stat .accent { color: var(--purple); }

.section-head {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 14px;
}
.section-head h2 { font-size: 16px; font-weight: 700; }
.section-head a  { font-size: 12.5px; color: var(--purple); text-decoration: none; }
.section-head a:hover { text-decoration: underline; }

.quick-actions { display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 10px; margin-bottom: 28px; }
.qa-card {
    background: var(--panel-2); border: 1px solid var(--border);
    border-radius: var(--radius-lg); padding: 16px 14px;
    text-align: center; text-decoration: none; color: var(--text-secondary);
    transition: border-color .15s, color .15s, background .15s;
    display: flex; flex-direction: column; align-items: center; gap: 9px;
}
.qa-card:hover { border-color: var(--border-hover); color: var(--text-primary); background: var(--panel-3); }
.qa-card svg { opacity: .7; }
.qa-card span { font-size: 12.5px; font-weight: 500; }

@media (max-width: 680px) { .dash-grid { grid-template-columns: 1fr 1fr; } }
@media (max-width: 420px) { .dash-grid { grid-template-columns: 1fr; } }
</style>

{{-- Welcome banner --}}
<div style="margin-bottom:28px;">
    <h1 style="font-size:24px;font-weight:700;letter-spacing:-.3px;">
        Welcome back, {{ explode(' ', auth()->user()->name)[0] }} 👋
    </h1>
    <p style="color:var(--text-secondary);font-size:13.5px;margin-top:5px;">
        Here's a quick look at your activity on BookMe.
    </p>
</div>

{{-- Stats --}}
<div class="dash-grid">
    <div class="dash-stat">
        <div class="label">Total Bookings</div>
        <div class="value">{{ $totalBookings }}</div>
        <div class="sub">All time</div>
    </div>
    <div class="dash-stat">
        <div class="label">Upcoming</div>
        <div class="value accent">{{ $upcomingCount }}</div>
        <div class="sub">Confirmed or pending</div>
    </div>
    <div class="dash-stat">
        <div class="label">Total Spent</div>
        <div class="value">₱{{ number_format($totalSpent, 0) }}</div>
        <div class="sub">Across all bookings</div>
    </div>
</div>

{{-- Quick actions --}}
<div class="section-head"><h2>Quick actions</h2></div>
<div class="quick-actions" style="margin-bottom:28px;">
    <a href="{{ route('listings.index') }}" class="qa-card">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
        <span>Browse listings</span>
    </a>
    <a href="{{ route('bookings.index') }}" class="qa-card">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        <span>My bookings</span>
    </a>
    <a href="{{ route('bookings.calendar') }}" class="qa-card">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        <span>Calendar</span>
    </a>
    <a href="{{ route('bookings.history') }}" class="qa-card">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><polyline points="12 8 12 12 14 14"/><path d="M3.05 11a9 9 0 1 0 .5-4.5"/><polyline points="3 3 3 7 7 7"/></svg>
        <span>History</span>
    </a>
    <a href="{{ route('profile.edit') }}" class="qa-card">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        <span>Edit profile</span>
    </a>
</div>

{{-- Upcoming bookings --}}
<div class="section-head">
    <h2>Upcoming bookings</h2>
    <a href="{{ route('bookings.index') }}">View all →</a>
</div>
<div class="bm-card" style="margin-bottom:28px;">
    @forelse($upcomingBookings as $booking)
        <a href="{{ route('bookings.show', $booking) }}" class="bm-list-row">
            <div class="bm-row-info">
                <div class="name">{{ $booking->listing->title }}</div>
                <div class="meta">
                    {{ $booking->business->name }} ·
                    {{ $booking->check_in->format('M j, Y') }}
                    @if($booking->check_out) — {{ $booking->check_out->format('M j, Y') }} @endif
                    · ₱{{ number_format($booking->total_price, 0) }}
                </div>
            </div>
            @php
                $badge = match($booking->status) {
                    'confirmed' => 'badge-green',
                    'pending'   => 'badge-amber',
                    'completed' => 'badge-gray',
                    default     => 'badge-red',
                };
            @endphp
            <span class="bm-row-badge {{ $badge }}">{{ ucfirst(str_replace('_',' ',$booking->status)) }}</span>
        </a>
    @empty
        <div class="bm-empty">
            <div class="icon">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </div>
            No upcoming bookings yet.
            <div style="margin-top:10px;"><a href="{{ route('listings.index') }}" class="bm-btn primary sm">Browse listings</a></div>
        </div>
    @endforelse
</div>

{{-- Recent activity --}}
@if($recentBookings->isNotEmpty())
<div class="section-head">
    <h2>Recent activity</h2>
    <a href="{{ route('bookings.history') }}">Full history →</a>
</div>
<div class="bm-card">
    @foreach($recentBookings as $booking)
        <a href="{{ route('bookings.show', $booking) }}" class="bm-list-row">
            <div class="bm-row-info">
                <div class="name">{{ $booking->listing->title }}</div>
                <div class="meta">{{ $booking->check_in->format('M j, Y') }} · ₱{{ number_format($booking->total_price, 0) }}</div>
            </div>
            @php
                $badge = match($booking->status) {
                    'confirmed' => 'badge-green', 'pending' => 'badge-amber',
                    'completed' => 'badge-gray', default => 'badge-red',
                };
            @endphp
            <span class="bm-row-badge {{ $badge }}">{{ ucfirst(str_replace('_',' ',$booking->status)) }}</span>
        </a>
    @endforeach
</div>
@endif

</x-layout>
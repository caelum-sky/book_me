<x-public-layout title="Booking History">

<style>
.hist-month { font-size:12px;font-weight:600;color:var(--text-tertiary);text-transform:uppercase;letter-spacing:.5px;margin:22px 0 10px;padding-bottom:8px;border-bottom:1px solid var(--border); }
.hist-month:first-child { margin-top:0; }
</style>

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:22px;flex-wrap:wrap;gap:12px;">
    <div>
        <h1 style="font-size:22px;font-weight:700;">Booking History</h1>
        <p style="color:var(--text-secondary);font-size:13px;margin-top:4px;">A complete log of your past reservations.</p>
    </div>
    <a href="{{ route('bookings.index') }}" class="bm-btn secondary sm">← Active bookings</a>
</div>

{{-- Summary stats --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:12px;margin-bottom:28px;">
    <div class="bm-stat-card" style="padding:16px 18px;">
        <div style="font-size:11.5px;color:var(--text-tertiary);margin-bottom:6px;text-transform:uppercase;letter-spacing:.4px;">Completed</div>
        <div style="font-size:24px;font-weight:700;color:var(--green);">{{ $completedCount }}</div>
    </div>
    <div class="bm-stat-card" style="padding:16px 18px;">
        <div style="font-size:11.5px;color:var(--text-tertiary);margin-bottom:6px;text-transform:uppercase;letter-spacing:.4px;">Cancelled</div>
        <div style="font-size:24px;font-weight:700;color:var(--red);">{{ $cancelledCount }}</div>
    </div>
    <div class="bm-stat-card" style="padding:16px 18px;">
        <div style="font-size:11.5px;color:var(--text-tertiary);margin-bottom:6px;text-transform:uppercase;letter-spacing:.4px;">Total spent</div>
        <div style="font-size:24px;font-weight:700;color:var(--purple);">₱{{ number_format($totalSpent, 0) }}</div>
    </div>
    <div class="bm-stat-card" style="padding:16px 18px;">
        <div style="font-size:11.5px;color:var(--text-tertiary);margin-bottom:6px;text-transform:uppercase;letter-spacing:.4px;">Places visited</div>
        <div style="font-size:24px;font-weight:700;">{{ $uniquePlaces }}</div>
    </div>
</div>

@forelse($bookingsByMonth as $monthLabel => $monthBookings)
    <div class="hist-month">{{ $monthLabel }}</div>
    <div class="bm-card" style="margin-bottom:4px;">
        @foreach($monthBookings as $booking)
            <a href="{{ route('bookings.show', $booking) }}" class="bm-list-row">
                <div style="display:flex;align-items:center;gap:13px;flex:1;min-width:0;">
                    <div style="width:44px;height:44px;border-radius:9px;overflow:hidden;background:var(--panel-3);flex-shrink:0;">
                        @if($booking->listing->primaryImage())
                            <img src="{{ $booking->listing->primaryImage()->url() }}" style="width:100%;height:100%;object-fit:cover;">
                        @else
                            <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:var(--text-tertiary);">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                            </div>
                        @endif
                    </div>
                    <div class="bm-row-info" style="min-width:0;">
                        <div class="name" style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $booking->listing->title }}</div>
                        <div class="meta">
                            {{ $booking->check_in->format('M j, Y') }}
                            @if($booking->check_out) — {{ $booking->check_out->format('M j, Y') }} @endif
                            · ₱{{ number_format($booking->total_price, 0) }}
                        </div>
                    </div>
                </div>
                <div style="display:flex;align-items:center;gap:8px;">
                    @if($booking->status === 'completed' && !$booking->review)
                        <a href="{{ route('bookings.review', $booking) }}" onclick="event.stopPropagation()" class="bm-btn secondary sm">Review</a>
                    @endif
                    @php
                        $badge = match($booking->status) {
                            'confirmed' => 'badge-green', 'pending' => 'badge-amber',
                            'completed' => 'badge-gray', default => 'badge-red',
                        };
                    @endphp
                    <span class="bm-row-badge {{ $badge }}">{{ ucfirst(str_replace('_',' ',$booking->status)) }}</span>
                </div>
            </a>
        @endforeach
    </div>
@empty
    <div class="bm-card">
        <div class="bm-empty">
            <div class="icon">
                <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><polyline points="12 8 12 12 14 14"/><path d="M3.05 11a9 9 0 1 0 .5-4.5"/><polyline points="3 3 3 7 7 7"/></svg>
            </div>
            No booking history yet.
            <div style="margin-top:12px;"><a href="{{ route('listings.index') }}" class="bm-btn primary sm">Start exploring</a></div>
        </div>
    </div>
@endforelse

@if(isset($bookings) && $bookings->hasPages())
    <div style="margin-top:24px;">{{ $bookings->links() }}</div>
@endif

</x-public-layout>

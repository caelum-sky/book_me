<x-layout title="My Bookings">

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:22px;flex-wrap:wrap;gap:12px;">
    <div>
        <h1 style="font-size:22px;font-weight:700;">My Bookings</h1>
        <p style="color:var(--text-secondary);font-size:13px;margin-top:4px;">All your reservation requests in one place.</p>
    </div>
    <a href="{{ route('listings.index') }}" class="bm-btn primary sm">+ New booking</a>
</div>

{{-- Status filter tabs --}}
<div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:20px;">
    @php $statuses = ['all','pending','confirmed','completed','cancelled','rejected']; @endphp
    @foreach($statuses as $s)
        <a href="{{ route('bookings.index', $s !== 'all' ? ['status' => $s] : []) }}"
           class="bm-tab {{ (request('status', 'all') === $s) ? 'active' : '' }}">
            {{ ucfirst($s) }}
        </a>
    @endforeach
</div>

<div class="bm-card">
    @forelse($bookings as $booking)
        <a href="{{ route('bookings.show', $booking) }}" class="bm-list-row">
            <div style="display:flex;align-items:center;gap:14px;flex:1;min-width:0;">
                {{-- Image thumbnail --}}
                <div style="width:52px;height:52px;border-radius:10px;overflow:hidden;background:var(--panel-3);flex-shrink:0;">
                    @if($booking->listing->primaryImage())
                        <img src="{{ $booking->listing->primaryImage()->url() }}" style="width:100%;height:100%;object-fit:cover;">
                    @else
                        <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:var(--text-tertiary);">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                        </div>
                    @endif
                </div>
                <div class="bm-row-info" style="min-width:0;">
                    <div class="name" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $booking->listing->title }}</div>
                    <div class="meta">
                        {{ $booking->business->name }} ·
                        {{ $booking->check_in->format('M j, Y') }}
                        @if($booking->check_out) — {{ $booking->check_out->format('M j, Y') }} @endif
                    </div>
                    <div style="font-size:13px;color:var(--text-secondary);margin-top:3px;font-weight:600;">
                        ₱{{ number_format($booking->total_price, 0) }}
                    </div>
                </div>
            </div>
            @php
                $badge = match($booking->status) {
                    'confirmed' => 'badge-green', 'pending' => 'badge-amber',
                    'completed' => 'badge-gray', default => 'badge-red',
                };
            @endphp
            <span class="bm-row-badge {{ $badge }}">{{ ucfirst(str_replace('_',' ',$booking->status)) }}</span>
        </a>
    @empty
        <div class="bm-empty">
            <div class="icon">
                <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            </div>
            @if(request('status') && request('status') !== 'all')
                No {{ request('status') }} bookings found.
            @else
                You haven't made any bookings yet.
            @endif
            <div style="margin-top:12px;">
                <a href="{{ route('listings.index') }}" class="bm-btn primary sm">Browse listings</a>
            </div>
        </div>
    @endforelse
</div>

@if($bookings->hasPages())
    <div style="margin-top:24px;">{{ $bookings->links() }}</div>
@endif

</x-layout>
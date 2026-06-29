<x-dashboard-layout title="My Bookings" active="bookings" subheading="All your reservation requests in one place.">
    <x-slot:search>
        <span>Browse stays, dining, and rooms from the public catalog.</span>
    </x-slot:search>

    <div class="bm-inline-actions" style="justify-content:space-between;">
        <div class="bm-tab-row">
            @php
                $statuses = ['all', 'pending', 'confirmed', 'completed', 'cancelled', 'rejected', 'no_show'];
            @endphp
            @foreach($statuses as $status)
                <a href="{{ route('bookings.index', $status !== 'all' ? ['status' => $status] : []) }}"
                   class="bm-tab {{ request('status', 'all') === $status ? 'active' : '' }}">
                    {{ ucfirst(str_replace('_', ' ', $status)) }}
                </a>
            @endforeach
        </div>
        <a href="{{ route('listings.index') }}" class="bm-btn primary sm">New booking</a>
    </div>

    <div class="bm-list-card">
        @forelse($bookings as $booking)
            <a href="{{ route('bookings.show', $booking) }}" class="bm-list-row">
                <div style="display:flex;align-items:center;gap:14px;flex:1;min-width:0;">
                    <div class="bm-media-thumb">
                        @if($booking->listing->primaryImage())
                            <img src="{{ $booking->listing->primaryImage()->url() }}" alt="{{ $booking->listing->title }}">
                        @else
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
                        @endif
                    </div>
                    <div class="bm-row-info" style="min-width:0;">
                        <div class="name" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $booking->listing->title }}</div>
                        <div class="meta">
                            {{ $booking->business->name }} &middot;
                            {{ $booking->check_in->format('M j, Y') }}
                            @if($booking->check_out) - {{ $booking->check_out->format('M j, Y') }} @endif
                        </div>
                        <div style="font-size:13px;color:var(--text-secondary);margin-top:3px;font-weight:700;">
                            &#8369;{{ number_format($booking->total_price, 0) }}
                        </div>
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
                    <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="18" rx="3"/><path d="M3 9h18M8 2v4M16 2v4"/></svg>
                </div>
                @if(request('status') && request('status') !== 'all')
                    No {{ str_replace('_', ' ', request('status')) }} bookings found.
                @else
                    You have not made any bookings yet.
                @endif
                <div style="margin-top:12px;">
                    <a href="{{ route('listings.index') }}" class="bm-btn primary sm">Browse listings</a>
                </div>
            </div>
        @endforelse
    </div>

    @if($bookings->hasPages())
        <div>{{ $bookings->links() }}</div>
    @endif
</x-dashboard-layout>

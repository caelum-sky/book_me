<x-public-layout title="Booking #{{ $booking->booking_reference }}">

<style>
.detail-grid { display: grid; grid-template-columns: 1fr 320px; gap: 28px; align-items: start; }
@media (max-width: 720px) { .detail-grid { grid-template-columns: 1fr; } }

.dl-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 14px; }
.dl-row dt { font-size: 12px; color: var(--text-tertiary); margin-bottom: 3px; font-weight: 500; text-transform: uppercase; letter-spacing: .4px; }
.dl-row dd { font-size: 14px; font-weight: 500; color: var(--text-primary); }

/* Receipt print styles */
@media print {
    body { background: white !important; color: black !important; }
    .bm-nav, .no-print, footer, #snow-canvas, .bg-glow { display: none !important; }
    .bm-page { padding: 0 !important; max-width: 100% !important; }
    .receipt-card { box-shadow: none !important; border: 1px solid #ddd !important; }
    .detail-grid { grid-template-columns: 1fr !important; }
}
</style>

<a href="{{ route('bookings.index') }}" style="font-size:12.5px;color:var(--text-tertiary);text-decoration:none;display:inline-flex;align-items:center;gap:5px;margin-bottom:20px;" class="no-print">
    ← My bookings
</a>

<div class="detail-grid">
    {{-- Main detail --}}
    <div>
        {{-- Header --}}
        <div class="bm-card" style="padding:22px;margin-bottom:16px;" id="receipt-card">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;">
                <div>
                    <div style="font-size:11.5px;font-weight:600;color:var(--purple);text-transform:uppercase;letter-spacing:.4px;margin-bottom:6px;">
                        Booking Reference
                    </div>
                    <div style="font-size:18px;font-weight:700;font-family:monospace;">
                        {{ $booking->booking_reference }}
                    </div>
                </div>
                @php
                    $badge = match($booking->status) {
                        'confirmed' => 'badge-green', 'pending' => 'badge-amber',
                        'completed' => 'badge-gray', default => 'badge-red',
                    };
                @endphp
                <span class="bm-row-badge {{ $badge }}" style="font-size:13px;padding:6px 14px;">
                    {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                </span>
            </div>

            <div style="border-top:1px solid var(--border);margin:18px 0;"></div>

            <h2 style="font-size:18px;font-weight:700;margin-bottom:4px;">{{ $booking->listing->title }}</h2>
            <div style="font-size:13px;color:var(--text-secondary);">{{ $booking->business->name }}</div>

            <div style="border-top:1px solid var(--border);margin:18px 0;"></div>

            <div class="dl-row">
                <div>
                    <dt>{{ $booking->listing->isRestaurant() ? 'Date' : 'Check-in' }}</dt>
                    <dd>{{ $booking->check_in->format('M j, Y') }}</dd>
                </div>
                <div>
                    @if($booking->reservation_time)
                        <dt>Time</dt>
                        <dd>{{ \Carbon\Carbon::parse($booking->reservation_time)->format('g:i A') }}</dd>
                    @elseif($booking->check_out)
                        <dt>Check-out</dt>
                        <dd>{{ $booking->check_out->format('M j, Y') }}</dd>
                    @endif
                </div>
            </div>

            <div class="dl-row">
                <div>
                    <dt>Guests</dt>
                    <dd>{{ $booking->guests }} {{ Str::plural('guest', $booking->guests) }}</dd>
                </div>
                @if($booking->unit)
                    <div>
                        <dt>{{ $booking->listing->isRestaurant() ? 'Table' : 'Unit' }}</dt>
                        <dd>{{ $booking->unit->name }}</dd>
                    </div>
                @endif
            </div>

            @if($booking->special_requests)
                <div style="border-top:1px solid var(--border);margin:14px 0 16px;padding-top:14px;">
                    <dt style="font-size:12px;color:var(--text-tertiary);font-weight:500;text-transform:uppercase;letter-spacing:.4px;margin-bottom:6px;">Special requests</dt>
                    <dd style="font-size:13.5px;color:var(--text-secondary);line-height:1.6;">{{ $booking->special_requests }}</dd>
                </div>
            @endif

            @if($booking->cancellation_reason)
                <div style="background:rgba(248,113,113,.08);border:1px solid rgba(248,113,113,.2);border-radius:var(--radius);padding:12px 14px;margin-top:14px;font-size:13px;color:var(--red);">
                    {{ $booking->cancellation_reason }}
                </div>
            @endif
        </div>

        {{-- Receipt (shown when paid/confirmed) --}}
        @if(in_array($booking->status, ['confirmed', 'completed']))
            <div class="bm-card receipt-card" style="padding:22px;margin-bottom:16px;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                    <h3 style="font-size:15px;font-weight:700;">Receipt</h3>
                    <div style="display:flex;gap:8px;" class="no-print">
                        <button onclick="window.print()" class="bm-btn secondary sm">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                            Print
                        </button>
                        <a href="{{ route('bookings.receipt', $booking) }}" class="bm-btn primary sm" download>
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                            Download PDF
                        </a>
                    </div>
                </div>

                <div style="border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;">
                    <div style="padding:12px 16px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;font-size:13px;">
                        <span style="color:var(--text-secondary);">Listing</span>
                        <span style="font-weight:500;">{{ $booking->listing->title }}</span>
                    </div>
                    @if($booking->unit)
                        <div style="padding:12px 16px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;font-size:13px;">
                            <span style="color:var(--text-secondary);">{{ $booking->listing->isRestaurant() ? 'Table' : 'Unit' }}</span>
                            <span style="font-weight:500;">{{ $booking->unit->name }}</span>
                        </div>
                    @endif
                    <div style="padding:12px 16px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;font-size:13px;">
                        <span style="color:var(--text-secondary);">Duration</span>
                        <span style="font-weight:500;">
                            @if($booking->check_out)
                                {{ $booking->check_in->diffInDays($booking->check_out) }} nights
                            @else
                                {{ $booking->reservation_time ? \Carbon\Carbon::parse($booking->reservation_time)->format('g:i A') : '—' }}
                            @endif
                        </span>
                    </div>
                    <div style="padding:12px 16px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;font-size:13px;">
                        <span style="color:var(--text-secondary);">Guests</span>
                        <span style="font-weight:500;">{{ $booking->guests }}</span>
                    </div>
                    <div style="padding:14px 16px;display:flex;justify-content:space-between;font-size:15px;">
                        <span style="font-weight:700;">Total</span>
                        <span style="font-weight:700;color:var(--purple);">₱{{ number_format($booking->total_price, 0) }}</span>
                    </div>
                </div>

                <div style="font-size:11.5px;color:var(--text-tertiary);margin-top:12px;text-align:center;">
                    Issued {{ $booking->updated_at->format('M j, Y') }} · BookMe
                </div>
            </div>
        @endif

        {{-- Cancel action --}}
        @if($booking->isCancellable())
            <div class="bm-card no-print" style="padding:18px;">
                <h3 style="font-size:14px;font-weight:600;margin-bottom:6px;">Cancel booking</h3>
                <p style="font-size:13px;color:var(--text-secondary);margin-bottom:14px;">
                    This will send a cancellation request. Once cancelled this cannot be undone.
                </p>
                <form method="POST" action="{{ route('bookings.cancel', $booking) }}"
                      onsubmit="return confirm('Cancel this booking?');">
                    @csrf
                    <button type="submit" class="bm-btn" style="background:rgba(248,113,113,.10);border:1px solid rgba(248,113,113,.25);color:var(--red);width:100%;height:38px;font-size:13.5px;font-weight:600;border-radius:var(--radius);cursor:pointer;font-family:inherit;">
                        Cancel this booking
                    </button>
                </form>
            </div>
        @endif

        {{-- Leave a review --}}
        @if($booking->status === 'completed' && !$booking->review)
            <div class="bm-card no-print" style="padding:18px;margin-top:14px;">
                <h3 style="font-size:14px;font-weight:600;margin-bottom:6px;">Leave a review</h3>
                <p style="font-size:13px;color:var(--text-secondary);margin-bottom:14px;">Share your experience to help other travellers.</p>
                <a href="{{ route('bookings.review', $booking) }}" class="bm-btn primary full">Write a review</a>
            </div>
        @endif
    </div>

    {{-- Sidebar --}}
    <div>
        <div class="bm-form-card no-print" style="margin-bottom:14px;">
            <div style="font-size:12px;color:var(--text-tertiary);font-weight:500;text-transform:uppercase;letter-spacing:.4px;margin-bottom:10px;">Property</div>
            <div style="font-size:14px;font-weight:600;margin-bottom:4px;">{{ $booking->business->name }}</div>
            @if($booking->business->contact_phone)
                <div style="font-size:12.5px;color:var(--text-secondary);margin-bottom:2px;">{{ $booking->business->contact_phone }}</div>
            @endif
            @if($booking->business->city)
                <div style="font-size:12.5px;color:var(--text-tertiary);">{{ $booking->business->city }}</div>
            @endif

            <div style="border-top:1px solid var(--border);margin:14px 0;"></div>

            <a href="{{ route('listings.show', $booking->listing) }}" class="bm-btn secondary full sm">
                View listing
            </a>
        </div>

        <div class="bm-form-card no-print">
            <div style="font-size:12px;color:var(--text-tertiary);font-weight:500;text-transform:uppercase;letter-spacing:.4px;margin-bottom:12px;">Your bookings</div>
            <a href="{{ route('bookings.index') }}" class="bm-btn secondary full sm" style="margin-bottom:8px;display:flex;">All bookings</a>
            <a href="{{ route('bookings.calendar') }}" class="bm-btn secondary full sm" style="display:flex;">View calendar</a>
        </div>
    </div>
</div>

</x-public-layout>
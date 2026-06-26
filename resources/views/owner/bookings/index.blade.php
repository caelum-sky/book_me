<x-dashboard-layout title="Bookings" active="bookings" subheading="Confirm, reject, and track reservations">

    <div class="bm-tab-row" style="margin-bottom:18px;">
        @foreach(['' => 'All', 'pending' => 'Pending', 'confirmed' => 'Confirmed', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $value => $label)
            <a href="{{ route('owner.bookings.index', $value ? ['status' => $value] : []) }}"
                class="bm-tab {{ request('status', '') === $value ? 'active' : '' }}">{{ $label }}</a>
        @endforeach
    </div>

    <div class="bm-list-card">
        @forelse($bookings as $booking)
            <div class="bm-list-row" style="align-items:flex-start;cursor:default;">
                <div class="bm-row-avatar" style="background:linear-gradient(135deg,#ec4faa,#7c5cff);">
                    {{ collect(explode(' ', $booking->user->name))->map(fn($p) => mb_substr($p,0,1))->take(2)->implode('') }}
                </div>
                <div class="bm-row-info">
                    <div class="name">{{ $booking->listing->title }}</div>
                    <div class="meta">
                        {{ $booking->user->name }} &middot;
                        {{ $booking->check_in->format('M j, Y') }}
                        @if($booking->check_out) &mdash; {{ $booking->check_out->format('M j, Y') }} @endif
                        @if($booking->reservation_time) at {{ \Carbon\Carbon::parse($booking->reservation_time)->format('g:i A') }} @endif
                    </div>
                    <div class="meta">{{ $booking->guests }} guests &middot; ₱{{ number_format($booking->total_price, 0) }}</div>
                    @if($booking->special_requests)
                        <div class="meta" style="font-style:italic;margin-top:2px;">"{{ $booking->special_requests }}"</div>
                    @endif

                    @if($booking->status === 'pending')
                        <div style="display:flex;gap:8px;margin-top:10px;">
                            <form method="POST" action="{{ route('owner.bookings.confirm', $booking) }}">
                                @csrf
                                <button type="submit" class="bm-btn success sm">Confirm</button>
                            </form>
                            <button type="button" class="bm-btn danger sm" onclick="bmOpenModal('modal-reject-{{ $booking->id }}')">Reject</button>
                        </div>
                    @elseif($booking->status === 'confirmed' && $booking->check_in->isPast())
                        <div style="display:flex;gap:8px;margin-top:10px;">
                            <form method="POST" action="{{ route('owner.bookings.complete', $booking) }}">
                                @csrf
                                <button type="submit" class="bm-btn secondary sm">Mark completed</button>
                            </form>
                            <form method="POST" action="{{ route('owner.bookings.no-show', $booking) }}">
                                @csrf
                                <button type="submit" class="bm-btn secondary sm">Mark no-show</button>
                            </form>
                        </div>
                    @endif
                </div>
                <div class="bm-row-badge
                    @if($booking->status === 'confirmed') badge-green
                    @elseif($booking->status === 'pending') badge-amber
                    @elseif($booking->status === 'completed') badge-gray
                    @else badge-red
                    @endif">
                    {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                </div>
            </div>

            @if($booking->status === 'pending')
                <dialog id="modal-reject-{{ $booking->id }}" class="bm-modal">
                    <div class="bm-modal-head">
                        <h2>Reject booking</h2>
                        <button type="button" class="bm-modal-close" onclick="bmCloseModal('modal-reject-{{ $booking->id }}')">&times;</button>
                    </div>
                    <form method="POST" action="{{ route('owner.bookings.reject', $booking) }}">
                        @csrf
                        <div class="bm-modal-body">
                            <div class="bm-field">
                                <label>Reason for rejection</label>
                                <input name="cancellation_reason" type="text" required placeholder="e.g. Dates no longer available">
                            </div>
                        </div>
                        <div class="bm-modal-foot">
                            <button type="button" class="bm-btn secondary" onclick="bmCloseModal('modal-reject-{{ $booking->id }}')">Cancel</button>
                            <button type="submit" class="bm-btn danger">Confirm rejection</button>
                        </div>
                    </form>
                </dialog>
            @endif
        @empty
            <div class="bm-empty">No bookings found for this filter.</div>
        @endforelse
    </div>

    <div style="margin-top:18px;">{{ $bookings->links() }}</div>

</x-dashboard-layout>
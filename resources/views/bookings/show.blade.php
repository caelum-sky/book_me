<x-layout title="Booking #{{ $booking->booking_reference }}">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <a href="{{ route('bookings.index') }}" class="text-sm text-zinc-500 hover:text-zinc-300">&larr; My bookings</a>

        <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-6 mt-4">
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="text-xl font-bold text-white">{{ $booking->listing->title }}</h1>
                    <p class="text-sm text-zinc-500 mt-1">Reference: {{ $booking->booking_reference }}</p>
                </div>
                <span class="text-xs font-medium px-2.5 py-1 rounded-full
                    @class([
                        'bg-green-950/50 text-green-400' => $booking->status === 'confirmed',
                        'bg-amber-950/50 text-amber-400' => $booking->status === 'pending',
                        'bg-zinc-800 text-zinc-400' => in_array($booking->status, ['completed']),
                        'bg-red-950/50 text-red-400' => in_array($booking->status, ['cancelled', 'rejected', 'no_show']),
                    ])">
                    {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                </span>
            </div>

            <div class="border-t border-zinc-800 my-5"></div>

            <dl class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <dt class="text-zinc-500">{{ $booking->listing->isRestaurant() ? 'Date' : 'Check-in' }}</dt>
                    <dd class="text-zinc-200 font-medium mt-0.5">{{ $booking->check_in->format('M j, Y') }}</dd>
                </div>
                @if($booking->reservation_time)
                    <div>
                        <dt class="text-zinc-500">Time</dt>
                        <dd class="text-zinc-200 font-medium mt-0.5">{{ \Carbon\Carbon::parse($booking->reservation_time)->format('g:i A') }}</dd>
                    </div>
                @elseif($booking->check_out)
                    <div>
                        <dt class="text-zinc-500">Check-out</dt>
                        <dd class="text-zinc-200 font-medium mt-0.5">{{ $booking->check_out->format('M j, Y') }}</dd>
                    </div>
                @endif
                <div>
                    <dt class="text-zinc-500">Guests</dt>
                    <dd class="text-zinc-200 font-medium mt-0.5">{{ $booking->guests }}</dd>
                </div>
                <div>
                    <dt class="text-zinc-500">Total</dt>
                    <dd class="text-zinc-200 font-medium mt-0.5">₱{{ number_format($booking->total_price, 0) }}</dd>
                </div>
            </dl>

            @if($booking->special_requests)
                <div class="border-t border-zinc-800 my-5"></div>
                <div>
                    <dt class="text-zinc-500 text-sm">Special requests</dt>
                    <dd class="text-zinc-300 text-sm mt-1">{{ $booking->special_requests }}</dd>
                </div>
            @endif

            @if($booking->cancellation_reason)
                <div class="border-t border-zinc-800 my-5"></div>
                <div class="rounded-lg bg-red-950/50 border border-red-900 text-red-400 px-4 py-3 text-sm">
                    {{ $booking->cancellation_reason }}
                </div>
            @endif

            <div class="border-t border-zinc-800 my-5"></div>

            <div class="text-sm">
                <div class="text-zinc-500">Business</div>
                <div class="text-zinc-200 font-medium mt-0.5">{{ $booking->business->name }}</div>
            </div>

            @if($booking->isCancellable())
                <form method="POST" action="{{ route('bookings.cancel', $booking) }}" class="mt-6"
                    onsubmit="return confirm('Cancel this booking?');">
                    @csrf
                    <button type="submit" class="w-full border border-red-900 text-red-400 rounded-lg py-2.5 text-sm font-medium hover:bg-red-950/50 transition">
                        Cancel booking
                    </button>
                </form>
            @endif
        </div>
    </div>
</x-layout>
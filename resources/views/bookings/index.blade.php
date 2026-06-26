<x-layout title="My Bookings">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <h1 class="text-2xl font-bold text-white mb-8">My Bookings</h1>

        @forelse($bookings as $booking)
            <a href="{{ route('bookings.show', $booking) }}" class="block bg-zinc-900 border border-zinc-800 rounded-xl p-4 mb-3 hover:border-zinc-700 transition">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="font-medium text-zinc-100">{{ $booking->listing->title }}</div>
                        <div class="text-sm text-zinc-500 mt-0.5">
                            {{ $booking->business->name }} &middot;
                            {{ $booking->check_in->format('M j, Y') }}
                            @if($booking->check_out)
                                &mdash; {{ $booking->check_out->format('M j, Y') }}
                            @endif
                        </div>
                        <div class="text-sm text-zinc-400 mt-1">₱{{ number_format($booking->total_price, 0) }}</div>
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
            </a>
        @empty
            <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-8 text-center">
                <p class="text-zinc-400 mb-4">You haven't made any bookings yet.</p>
                <a href="{{ route('listings.index') }}" class="text-blue-400 hover:text-blue-300 text-sm font-medium">Browse listings &rarr;</a>
            </div>
        @endforelse

        <div class="mt-6">
            {{ $bookings->links() }}
        </div>
    </div>
</x-layout>
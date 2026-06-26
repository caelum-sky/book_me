<x-layout title="Dashboard">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <h1 class="text-2xl font-bold text-white mb-1">Welcome back, {{ auth()->user()->name }}</h1>
        <p class="text-zinc-400 mb-8">Here's what's coming up.</p>

        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-white">Upcoming bookings</h2>
            <a href="{{ route('bookings.index') }}" class="text-sm text-blue-400 hover:text-blue-300">View all</a>
        </div>

        @forelse($upcomingBookings as $booking)
            <a href="{{ route('bookings.show', $booking) }}" class="block bg-zinc-900 border border-zinc-800 rounded-xl p-4 mb-3 hover:border-zinc-700 transition">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="font-medium text-zinc-100">{{ $booking->listing->title }}</div>
                        <div class="text-sm text-zinc-500 mt-0.5">
                            {{ $booking->check_in->format('M j, Y') }}
                            @if($booking->check_out)
                                &mdash; {{ $booking->check_out->format('M j, Y') }}
                            @endif
                        </div>
                    </div>
                    <span class="text-xs font-medium px-2.5 py-1 rounded-full
                        {{ $booking->status === 'confirmed' ? 'bg-green-950/50 text-green-400' : 'bg-amber-950/50 text-amber-400' }}">
                        {{ ucfirst($booking->status) }}
                    </span>
                </div>
            </a>
        @empty
            <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-8 text-center mb-8">
                <p class="text-zinc-400 mb-4">No upcoming bookings yet.</p>
                <a href="{{ route('listings.index') }}" class="text-blue-400 hover:text-blue-300 text-sm font-medium">Browse listings &rarr;</a>
            </div>
        @endforelse

        @if($pastBookings->isNotEmpty())
            <h2 class="font-semibold text-white mt-10 mb-4">Past bookings</h2>
            @foreach($pastBookings as $booking)
                <a href="{{ route('bookings.show', $booking) }}" class="block bg-zinc-900 border border-zinc-800 rounded-xl p-4 mb-3 hover:border-zinc-700 transition opacity-75">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="font-medium text-zinc-200">{{ $booking->listing->title }}</div>
                            <div class="text-sm text-zinc-500 mt-0.5">{{ $booking->check_in->format('M j, Y') }}</div>
                        </div>
                        <span class="text-xs font-medium px-2.5 py-1 rounded-full bg-zinc-800 text-zinc-400">
                            {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                        </span>
                    </div>
                </a>
            @endforeach
        @endif
    </div>
</x-layout>
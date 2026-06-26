<x-layout title="Book {{ $listing->title }}">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <a href="{{ route('listings.show', $listing) }}" class="text-sm text-zinc-500 hover:text-zinc-300">&larr; Back to listing</a>

        <h1 class="text-2xl font-bold text-white mt-3 mb-1">Book {{ $listing->title }}</h1>
        <p class="text-zinc-400 mb-8">Fill in your details to request a reservation.</p>

        @if($listing->units->isEmpty())
            <div class="rounded-lg bg-amber-950/50 border border-amber-900 text-amber-400 px-4 py-3 text-sm">
                This listing doesn't have any bookable units available right now.
            </div>
        @else
            <form method="POST" action="{{ route('bookings.store', $listing) }}" class="space-y-5 bg-zinc-900 border border-zinc-800 rounded-xl p-6">
                @csrf

                <div>
                    <label for="listing_unit_id" class="block text-sm font-medium mb-1 text-zinc-200">
                        {{ $listing->isRestaurant() ? 'Table' : 'Room / Unit' }}
                    </label>
                    <select id="listing_unit_id" name="listing_unit_id" required
                        class="w-full rounded-lg border border-zinc-700 bg-zinc-950 text-zinc-100 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Select an option</option>
                        @foreach($listing->units as $unit)
                            <option value="{{ $unit->id }}" {{ old('listing_unit_id') == $unit->id ? 'selected' : '' }}>
                                {{ $unit->name }} &mdash; up to {{ $unit->capacity }} guests
                                &mdash; ₱{{ number_format($unit->effectivePrice(), 0) }}
                            </option>
                        @endforeach
                    </select>
                    @error('listing_unit_id') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="check_in" class="block text-sm font-medium mb-1 text-zinc-200">
                            {{ $listing->isRestaurant() ? 'Date' : 'Check-in' }}
                        </label>
                        <input id="check_in" name="check_in" type="date" required value="{{ old('check_in') }}"
                            min="{{ now()->toDateString() }}"
                            class="w-full rounded-lg border border-zinc-700 bg-zinc-950 text-zinc-100 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('check_in') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    @if($listing->isRestaurant())
                        <div>
                            <label for="reservation_time" class="block text-sm font-medium mb-1 text-zinc-200">Time</label>
                            <input id="reservation_time" name="reservation_time" type="time" required value="{{ old('reservation_time') }}"
                                class="w-full rounded-lg border border-zinc-700 bg-zinc-950 text-zinc-100 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('reservation_time') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    @else
                        <div>
                            <label for="check_out" class="block text-sm font-medium mb-1 text-zinc-200">Check-out</label>
                            <input id="check_out" name="check_out" type="date" required value="{{ old('check_out') }}"
                                min="{{ now()->addDay()->toDateString() }}"
                                class="w-full rounded-lg border border-zinc-700 bg-zinc-950 text-zinc-100 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('check_out') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    @endif
                </div>

                <div>
                    <label for="guests" class="block text-sm font-medium mb-1 text-zinc-200">Number of guests</label>
                    <input id="guests" name="guests" type="number" min="1" required value="{{ old('guests', 1) }}"
                        class="w-full rounded-lg border border-zinc-700 bg-zinc-950 text-zinc-100 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('guests') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="special_requests" class="block text-sm font-medium mb-1 text-zinc-200">
                        Special requests <span class="text-zinc-500 font-normal">(optional)</span>
                    </label>
                    <textarea id="special_requests" name="special_requests" rows="3"
                        class="w-full rounded-lg border border-zinc-700 bg-zinc-950 text-zinc-100 px-3 py-2 text-sm placeholder-zinc-500 focus:border-blue-500 focus:ring-blue-500">{{ old('special_requests') }}</textarea>
                    @error('special_requests') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <button type="submit" class="w-full bg-blue-600 text-white rounded-lg py-2.5 text-sm font-medium hover:bg-blue-500 transition">
                    Submit booking request
                </button>

                <p class="text-xs text-zinc-500 text-center">
                    Your booking will be pending until the owner confirms it.
                </p>
            </form>
        @endif
    </div>
</x-layout>
<x-layout title="Edit {{ $listing->title }}">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="flex items-center justify-between mb-1">
            <h1 class="text-2xl font-bold text-white">{{ $listing->title }}</h1>
            <span class="text-xs font-medium px-2.5 py-1 rounded-full
                @class([
                    'bg-green-950/50 text-green-400' => $listing->status === 'published',
                    'bg-amber-950/50 text-amber-400' => $listing->status === 'pending_review',
                    'bg-zinc-800 text-zinc-400' => $listing->status === 'draft',
                    'bg-red-950/50 text-red-400' => in_array($listing->status, ['rejected', 'suspended']),
                ])">
                {{ ucfirst(str_replace('_', ' ', $listing->status)) }}
            </span>
        </div>
        <p class="text-zinc-400 text-sm mb-8">
            <a href="{{ route('owner.listings.calendar', $listing) }}" class="text-blue-400 hover:text-blue-300">View calendar &rarr;</a>
        </p>

        @if($listing->status === 'rejected' && $listing->rejection_reason)
            <div class="rounded-lg bg-red-950/50 border border-red-900 text-red-400 px-4 py-3 text-sm mb-6">
                <strong>Rejected:</strong> {{ $listing->rejection_reason }}
            </div>
        @endif

        {{-- Core details --}}
        <form method="POST" action="{{ route('owner.listings.update', $listing) }}" enctype="multipart/form-data" class="space-y-5 bg-zinc-900 border border-zinc-800 rounded-xl p-6">
            @csrf
            @method('PUT')

            <div>
                <label for="type" class="block text-sm font-medium mb-1 text-zinc-200">Type</label>
                <select id="type" name="type" required onchange="toggleTypeFields(this.value)"
                    class="w-full rounded-lg border border-zinc-700 bg-zinc-950 text-zinc-100 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                    @foreach($types as $type)
                        <option value="{{ $type }}" {{ old('type', $listing->type) === $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                    @endforeach
                </select>
                @error('type') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="title" class="block text-sm font-medium mb-1 text-zinc-200">Title</label>
                <input id="title" name="title" type="text" value="{{ old('title', $listing->title) }}" required
                    class="w-full rounded-lg border border-zinc-700 bg-zinc-950 text-zinc-100 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                @error('title') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium mb-1 text-zinc-200">Description</label>
                <textarea id="description" name="description" rows="4"
                    class="w-full rounded-lg border border-zinc-700 bg-zinc-950 text-zinc-100 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $listing->description) }}</textarea>
            </div>

            <div>
                <label for="address" class="block text-sm font-medium mb-1 text-zinc-200">Address</label>
                <input id="address" name="address" type="text" value="{{ old('address', $listing->address) }}"
                    class="w-full rounded-lg border border-zinc-700 bg-zinc-950 text-zinc-100 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="city" class="block text-sm font-medium mb-1 text-zinc-200">City</label>
                    <input id="city" name="city" type="text" value="{{ old('city', $listing->city) }}"
                        class="w-full rounded-lg border border-zinc-700 bg-zinc-950 text-zinc-100 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label for="province" class="block text-sm font-medium mb-1 text-zinc-200">Province</label>
                    <input id="province" name="province" type="text" value="{{ old('province', $listing->province) }}"
                        class="w-full rounded-lg border border-zinc-700 bg-zinc-950 text-zinc-100 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            <div id="accommodation-fields" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="max_guests" class="block text-sm font-medium mb-1 text-zinc-200">Max guests</label>
                    <input id="max_guests" name="max_guests" type="number" min="1" value="{{ old('max_guests', $listing->max_guests) }}"
                        class="w-full rounded-lg border border-zinc-700 bg-zinc-950 text-zinc-100 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label for="bedrooms" class="block text-sm font-medium mb-1 text-zinc-200">Bedrooms</label>
                    <input id="bedrooms" name="bedrooms" type="number" min="0" value="{{ old('bedrooms', $listing->bedrooms) }}"
                        class="w-full rounded-lg border border-zinc-700 bg-zinc-950 text-zinc-100 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label for="bathrooms" class="block text-sm font-medium mb-1 text-zinc-200">Bathrooms</label>
                    <input id="bathrooms" name="bathrooms" type="number" min="0" value="{{ old('bathrooms', $listing->bathrooms) }}"
                        class="w-full rounded-lg border border-zinc-700 bg-zinc-950 text-zinc-100 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            <div id="restaurant-fields" class="hidden grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="seating_capacity" class="block text-sm font-medium mb-1 text-zinc-200">Seating capacity</label>
                    <input id="seating_capacity" name="seating_capacity" type="number" min="1" value="{{ old('seating_capacity', $listing->seating_capacity) }}"
                        class="w-full rounded-lg border border-zinc-700 bg-zinc-950 text-zinc-100 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label for="cuisine_type" class="block text-sm font-medium mb-1 text-zinc-200">Cuisine type</label>
                    <input id="cuisine_type" name="cuisine_type" type="text" value="{{ old('cuisine_type', $listing->cuisine_type) }}"
                        class="w-full rounded-lg border border-zinc-700 bg-zinc-950 text-zinc-100 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="base_price" class="block text-sm font-medium mb-1 text-zinc-200">Base price (₱)</label>
                    <input id="base_price" name="base_price" type="number" min="0" step="0.01" value="{{ old('base_price', $listing->base_price) }}" required
                        class="w-full rounded-lg border border-zinc-700 bg-zinc-950 text-zinc-100 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label for="price_unit" class="block text-sm font-medium mb-1 text-zinc-200">Price unit</label>
                    <select id="price_unit" name="price_unit" required
                        class="w-full rounded-lg border border-zinc-700 bg-zinc-950 text-zinc-100 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="per_night" {{ old('price_unit', $listing->price_unit) === 'per_night' ? 'selected' : '' }}>Per night</option>
                        <option value="per_reservation" {{ old('price_unit', $listing->price_unit) === 'per_reservation' ? 'selected' : '' }}>Per reservation</option>
                        <option value="per_person" {{ old('price_unit', $listing->price_unit) === 'per_person' ? 'selected' : '' }}>Per person</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="images" class="block text-sm font-medium mb-1 text-zinc-200">Add photos</label>
                <input id="images" name="images[]" type="file" accept="image/*" multiple
                    class="w-full text-sm text-zinc-300 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-zinc-800 file:text-zinc-200 file:text-sm hover:file:bg-zinc-700">
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white rounded-lg py-2.5 text-sm font-medium hover:bg-blue-500 transition">
                Save changes
            </button>
        </form>

        {{-- Existing images --}}
        @if($listing->images->isNotEmpty())
            <div class="mt-8">
                <h2 class="font-semibold text-white mb-3">Photos</h2>
                <div class="grid grid-cols-3 sm:grid-cols-4 gap-3">
                    @foreach($listing->images as $image)
                        <div class="relative aspect-square rounded-lg overflow-hidden bg-zinc-800 group">
                            <img src="{{ $image->url() }}" class="w-full h-full object-cover">
                            <form method="POST" action="{{ route('owner.listings.images.destroy', [$listing, $image]) }}"
                                class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition"
                                onsubmit="return confirm('Remove this photo?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-600 text-white text-xs rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-500">&times;</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Units / Rooms management --}}
        <div class="mt-10">
            <h2 class="font-semibold text-white mb-3">{{ $listing->isRestaurant() ? 'Tables' : 'Rooms / Units' }}</h2>

            @foreach($listing->units as $unit)
                <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-4 mb-3 flex items-center justify-between gap-4">
                    <div class="text-sm">
                        <div class="font-medium text-zinc-100">{{ $unit->name }}</div>
                        <div class="text-zinc-500 mt-0.5">
                            Capacity {{ $unit->capacity }} &middot; Qty {{ $unit->quantity }}
                            @if($unit->price_override)
                                &middot; ₱{{ number_format($unit->price_override, 0) }} override
                            @endif
                        </div>
                    </div>
                    <form method="POST" action="{{ route('owner.listings.units.destroy', [$listing, $unit]) }}"
                        onsubmit="return confirm('Remove this unit?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm text-red-400 hover:text-red-300">Remove</button>
                    </form>
                </div>
            @endforeach

            <form method="POST" action="{{ route('owner.listings.units.store', $listing) }}" class="bg-zinc-900 border border-zinc-800 rounded-xl p-4 mt-3">
                @csrf
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <input name="name" type="text" placeholder="{{ $listing->isRestaurant() ? 'Table name' : 'Room name' }}" required
                        class="rounded-lg border border-zinc-700 bg-zinc-950 text-zinc-100 px-3 py-2 text-sm placeholder-zinc-500 focus:border-blue-500 focus:ring-blue-500">
                    <input name="capacity" type="number" min="1" placeholder="Capacity" required
                        class="rounded-lg border border-zinc-700 bg-zinc-950 text-zinc-100 px-3 py-2 text-sm placeholder-zinc-500 focus:border-blue-500 focus:ring-blue-500">
                    <input name="quantity" type="number" min="1" value="1" placeholder="Quantity" required
                        class="rounded-lg border border-zinc-700 bg-zinc-950 text-zinc-100 px-3 py-2 text-sm placeholder-zinc-500 focus:border-blue-500 focus:ring-blue-500">
                    <input name="price_override" type="number" min="0" step="0.01" placeholder="Price override (optional)"
                        class="rounded-lg border border-zinc-700 bg-zinc-950 text-zinc-100 px-3 py-2 text-sm placeholder-zinc-500 focus:border-blue-500 focus:ring-blue-500">
                </div>
                <button type="submit" class="mt-3 bg-zinc-800 text-zinc-200 rounded-lg px-4 py-2 text-sm font-medium hover:bg-zinc-700 transition">
                    + Add {{ $listing->isRestaurant() ? 'table' : 'unit' }}
                </button>
            </form>
        </div>

        {{-- Publish action --}}
        @if($listing->status === 'draft')
            <form method="POST" action="{{ route('owner.listings.publish', $listing) }}" class="mt-8">
                @csrf
                <button type="submit" class="w-full bg-green-600 text-white rounded-lg py-2.5 text-sm font-medium hover:bg-green-500 transition">
                    Submit for review
                </button>
            </form>
        @endif

        <form method="POST" action="{{ route('owner.listings.destroy', $listing) }}" class="mt-3"
            onsubmit="return confirm('Delete this listing permanently?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="w-full border border-red-900 text-red-400 rounded-lg py-2.5 text-sm font-medium hover:bg-red-950/50 transition">
                Delete listing
            </button>
        </form>
    </div>

    <script>
        function toggleTypeFields(type) {
            const isRestaurant = type === 'restaurant';
            document.getElementById('accommodation-fields').classList.toggle('hidden', isRestaurant);
            document.getElementById('accommodation-fields').classList.toggle('grid', !isRestaurant);
            document.getElementById('restaurant-fields').classList.toggle('hidden', !isRestaurant);
            document.getElementById('restaurant-fields').classList.toggle('grid', isRestaurant);
        }
        document.addEventListener('DOMContentLoaded', () => toggleTypeFields(document.getElementById('type').value));
    </script>
</x-layout>
<x-layout title="New Listing">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <h1 class="text-2xl font-bold text-white mb-1">Create a new listing</h1>
        <p class="text-zinc-400 text-sm mb-8">This will be submitted for super-admin review before going live.</p>

        <form method="POST" action="{{ route('owner.listings.store') }}" enctype="multipart/form-data" class="space-y-5 bg-zinc-900 border border-zinc-800 rounded-xl p-6">
            @csrf

            <div>
                <label for="type" class="block text-sm font-medium mb-1 text-zinc-200">Type</label>
                <select id="type" name="type" required onchange="toggleTypeFields(this.value)"
                    class="w-full rounded-lg border border-zinc-700 bg-zinc-950 text-zinc-100 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Select a type</option>
                    @foreach($types as $type)
                        <option value="{{ $type }}" {{ old('type') === $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                    @endforeach
                </select>
                @error('type') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="title" class="block text-sm font-medium mb-1 text-zinc-200">Title</label>
                <input id="title" name="title" type="text" value="{{ old('title') }}" required
                    class="w-full rounded-lg border border-zinc-700 bg-zinc-950 text-zinc-100 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                @error('title') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium mb-1 text-zinc-200">Description</label>
                <textarea id="description" name="description" rows="4"
                    class="w-full rounded-lg border border-zinc-700 bg-zinc-950 text-zinc-100 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea>
                @error('description') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="address" class="block text-sm font-medium mb-1 text-zinc-200">Address</label>
                <input id="address" name="address" type="text" value="{{ old('address') }}"
                    class="w-full rounded-lg border border-zinc-700 bg-zinc-950 text-zinc-100 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="city" class="block text-sm font-medium mb-1 text-zinc-200">City</label>
                    <input id="city" name="city" type="text" value="{{ old('city') }}"
                        class="w-full rounded-lg border border-zinc-700 bg-zinc-950 text-zinc-100 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label for="province" class="block text-sm font-medium mb-1 text-zinc-200">Province</label>
                    <input id="province" name="province" type="text" value="{{ old('province') }}"
                        class="w-full rounded-lg border border-zinc-700 bg-zinc-950 text-zinc-100 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            <div id="accommodation-fields" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="max_guests" class="block text-sm font-medium mb-1 text-zinc-200">Max guests</label>
                    <input id="max_guests" name="max_guests" type="number" min="1" value="{{ old('max_guests') }}"
                        class="w-full rounded-lg border border-zinc-700 bg-zinc-950 text-zinc-100 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('max_guests') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="bedrooms" class="block text-sm font-medium mb-1 text-zinc-200">Bedrooms</label>
                    <input id="bedrooms" name="bedrooms" type="number" min="0" value="{{ old('bedrooms') }}"
                        class="w-full rounded-lg border border-zinc-700 bg-zinc-950 text-zinc-100 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label for="bathrooms" class="block text-sm font-medium mb-1 text-zinc-200">Bathrooms</label>
                    <input id="bathrooms" name="bathrooms" type="number" min="0" value="{{ old('bathrooms') }}"
                        class="w-full rounded-lg border border-zinc-700 bg-zinc-950 text-zinc-100 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            <div id="restaurant-fields" class="hidden grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="seating_capacity" class="block text-sm font-medium mb-1 text-zinc-200">Seating capacity</label>
                    <input id="seating_capacity" name="seating_capacity" type="number" min="1" value="{{ old('seating_capacity') }}"
                        class="w-full rounded-lg border border-zinc-700 bg-zinc-950 text-zinc-100 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('seating_capacity') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="cuisine_type" class="block text-sm font-medium mb-1 text-zinc-200">Cuisine type</label>
                    <input id="cuisine_type" name="cuisine_type" type="text" value="{{ old('cuisine_type') }}"
                        class="w-full rounded-lg border border-zinc-700 bg-zinc-950 text-zinc-100 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="base_price" class="block text-sm font-medium mb-1 text-zinc-200">Base price (₱)</label>
                    <input id="base_price" name="base_price" type="number" min="0" step="0.01" value="{{ old('base_price') }}" required
                        class="w-full rounded-lg border border-zinc-700 bg-zinc-950 text-zinc-100 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('base_price') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="price_unit" class="block text-sm font-medium mb-1 text-zinc-200">Price unit</label>
                    <select id="price_unit" name="price_unit" required
                        class="w-full rounded-lg border border-zinc-700 bg-zinc-950 text-zinc-100 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="per_night" {{ old('price_unit') === 'per_night' ? 'selected' : '' }}>Per night</option>
                        <option value="per_reservation" {{ old('price_unit') === 'per_reservation' ? 'selected' : '' }}>Per reservation</option>
                        <option value="per_person" {{ old('price_unit') === 'per_person' ? 'selected' : '' }}>Per person</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="images" class="block text-sm font-medium mb-1 text-zinc-200">Photos</label>
                <input id="images" name="images[]" type="file" accept="image/*" multiple
                    class="w-full text-sm text-zinc-300 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-zinc-800 file:text-zinc-200 file:text-sm hover:file:bg-zinc-700">
                @error('images.*') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white rounded-lg py-2.5 text-sm font-medium hover:bg-blue-500 transition">
                Create listing
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
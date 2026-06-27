{{--
    resources/views/admin/listings/_form.blade.php
    Shared fields for admin's create and edit listing modals.
    Expects: $listing (null when creating)
--}}
@php
    $uid = $listing ? 'admin-edit-'.$listing->id : 'admin-new';
@endphp

<div class="bm-field">
    <label for="type-{{ $uid }}">Type</label>
    <select id="type-{{ $uid }}" name="type" required onchange="bmToggleListingType(this)">
        <option value="">Select a type</option>
        @foreach(\App\Models\Listing::TYPES as $type)
            <option value="{{ $type }}" {{ old('type', $listing->type ?? '') === $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
        @endforeach
    </select>
    @error('type') <div class="error">{{ $message }}</div> @enderror
</div>

<div class="bm-field">
    <label for="title-{{ $uid }}">Title</label>
    <input id="title-{{ $uid }}" name="title" type="text" value="{{ old('title', $listing->title ?? '') }}" required>
    @error('title') <div class="error">{{ $message }}</div> @enderror
</div>

<div class="bm-field">
    <label for="description-{{ $uid }}">Description</label>
    <textarea id="description-{{ $uid }}" name="description" rows="3">{{ old('description', $listing->description ?? '') }}</textarea>
</div>

<div class="bm-field">
    <label for="address-{{ $uid }}">Address</label>
    <input id="address-{{ $uid }}" name="address" type="text" value="{{ old('address', $listing->address ?? '') }}">
</div>

<div class="bm-field-row">
    <div class="bm-field">
        <label for="city-{{ $uid }}">City</label>
        <input id="city-{{ $uid }}" name="city" type="text" value="{{ old('city', $listing->city ?? '') }}">
    </div>
    <div class="bm-field">
        <label for="province-{{ $uid }}">Province</label>
        <input id="province-{{ $uid }}" name="province" type="text" value="{{ old('province', $listing->province ?? '') }}">
    </div>
</div>

<div data-accommodation-fields class="bm-field-row cols-3">
    <div class="bm-field">
        <label for="max_guests-{{ $uid }}">Max guests</label>
        <input id="max_guests-{{ $uid }}" name="max_guests" type="number" min="1" value="{{ old('max_guests', $listing->max_guests ?? '') }}">
    </div>
    <div class="bm-field">
        <label for="bedrooms-{{ $uid }}">Bedrooms</label>
        <input id="bedrooms-{{ $uid }}" name="bedrooms" type="number" min="0" value="{{ old('bedrooms', $listing->bedrooms ?? '') }}">
    </div>
    <div class="bm-field">
        <label for="bathrooms-{{ $uid }}">Bathrooms</label>
        <input id="bathrooms-{{ $uid }}" name="bathrooms" type="number" min="0" value="{{ old('bathrooms', $listing->bathrooms ?? '') }}">
    </div>
</div>

<div data-restaurant-fields class="bm-field-row" style="display:none;">
    <div class="bm-field">
        <label for="seating_capacity-{{ $uid }}">Seating capacity</label>
        <input id="seating_capacity-{{ $uid }}" name="seating_capacity" type="number" min="1" value="{{ old('seating_capacity', $listing->seating_capacity ?? '') }}">
    </div>
    <div class="bm-field">
        <label for="cuisine_type-{{ $uid }}">Cuisine type</label>
        <input id="cuisine_type-{{ $uid }}" name="cuisine_type" type="text" value="{{ old('cuisine_type', $listing->cuisine_type ?? '') }}">
    </div>
</div>

<div class="bm-field-row">
    <div class="bm-field">
        <label for="base_price-{{ $uid }}">Base price (₱)</label>
        <input id="base_price-{{ $uid }}" name="base_price" type="number" min="0" step="0.01" value="{{ old('base_price', $listing->base_price ?? '') }}" required>
        @error('base_price') <div class="error">{{ $message }}</div> @enderror
    </div>
    <div class="bm-field">
        <label for="price_unit-{{ $uid }}">Price unit</label>
        <select id="price_unit-{{ $uid }}" name="price_unit" required>
            <option value="per_night" {{ old('price_unit', $listing->price_unit ?? '') === 'per_night' ? 'selected' : '' }}>Per night</option>
            <option value="per_reservation" {{ old('price_unit', $listing->price_unit ?? '') === 'per_reservation' ? 'selected' : '' }}>Per reservation</option>
            <option value="per_person" {{ old('price_unit', $listing->price_unit ?? '') === 'per_person' ? 'selected' : '' }}>Per person</option>
        </select>
    </div>
</div>

<div class="bm-field">
    <label for="images-{{ $uid }}">{{ $listing ? 'Add more photos' : 'Photos' }}</label>
    <input id="images-{{ $uid }}" name="images[]" type="file" accept="image/*" multiple
        style="background:var(--panel);border:1px solid var(--border-soft);border-radius:10px;padding:9px;color:var(--text-secondary);font-size:12.5px;width:100%;">
</div>

<script>
    function bmToggleListingType(selectEl) {
        const form = selectEl.closest('form');
        if (!form) return;
        const isRestaurant = selectEl.value === 'restaurant';
        const accom = form.querySelector('[data-accommodation-fields]');
        const rest = form.querySelector('[data-restaurant-fields]');
        if (accom) accom.style.display = isRestaurant ? 'none' : 'grid';
        if (rest) rest.style.display = isRestaurant ? 'grid' : 'none';
    }
    (function () {
        const sel = document.getElementById('type-{{ $uid }}');
        if (sel) bmToggleListingType(sel);
    })();
</script>
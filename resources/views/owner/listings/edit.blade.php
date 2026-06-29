@php
    $statusBadge = match($listing->status) {
        'published' => 'badge-green',
        'pending_review' => 'badge-amber',
        'draft' => 'badge-gray',
        default => 'badge-red',
    };
    $gallery = $listing->gallery();
@endphp

<x-dashboard-layout title="Edit {{ $listing->title }}" active="listings" :heading="$listing->title" subheading="Update details, photos, units, and review status.">
    <div class="bm-inline-actions" style="justify-content:space-between;">
        <div class="bm-inline-actions">
            <span class="bm-row-badge {{ $statusBadge }}">{{ ucfirst(str_replace('_', ' ', $listing->status)) }}</span>
            <a href="{{ route('owner.listings.calendar', $listing) }}" class="bm-btn secondary sm">View calendar</a>
        </div>
        <a href="{{ route('owner.listings.index') }}" class="bm-btn secondary sm">Back to listings</a>
    </div>

    @if($listing->status === 'rejected' && $listing->rejection_reason)
        <div class="bm-alert error">
            <span><strong>Rejected:</strong> {{ $listing->rejection_reason }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('owner.listings.update', $listing) }}" enctype="multipart/form-data" class="bm-form-card" style="max-width:860px;">
        @csrf
        @method('PUT')

        @include('owner.listings._form', ['listing' => $listing, 'types' => $types])

        <button type="submit" class="bm-btn primary full">Save changes</button>
    </form>

    @if($gallery->isNotEmpty())
        <div class="bm-list-card">
            <div class="bm-list-head">
                <h2>Photos</h2>
            </div>
            <div class="bm-media-grid">
                @foreach($gallery as $image)
                    <div class="bm-media-tile">
                        <img src="{{ $image->url() }}" alt="{{ $listing->title }}">
                        <form method="POST" action="{{ route('owner.listings.images.destroy', [$listing, $image]) }}" onsubmit="return confirm('Remove this photo?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bm-btn danger sm" aria-label="Remove photo">&times;</button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="bm-list-card">
        <div class="bm-list-head">
            <h2>{{ $listing->isRestaurant() ? 'Tables' : 'Rooms / Units' }}</h2>
        </div>

        @forelse($listing->units as $unit)
            <div class="bm-list-row" style="cursor:default;">
                <div class="bm-row-info">
                    <div class="name">{{ $unit->name }}</div>
                    <div class="meta">
                        Capacity {{ $unit->capacity }} &middot; Qty {{ $unit->quantity }}
                        @if($unit->price_override)
                            &middot; &#8369;{{ number_format($unit->price_override, 0) }} override
                        @endif
                    </div>
                </div>
                <form method="POST" action="{{ route('owner.listings.units.destroy', [$listing, $unit]) }}" onsubmit="return confirm('Remove this unit?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bm-btn danger sm">Remove</button>
                </form>
            </div>
        @empty
            <div class="bm-empty">No {{ $listing->isRestaurant() ? 'tables' : 'rooms or units' }} yet.</div>
        @endforelse

        <form method="POST" action="{{ route('owner.listings.units.store', $listing) }}" class="bm-form-card" style="margin-top:16px;">
            @csrf
            <div class="bm-field-row cols-4">
                <div class="bm-field">
                    <label for="unit-name">{{ $listing->isRestaurant() ? 'Table name' : 'Room name' }}</label>
                    <input id="unit-name" name="name" type="text" required>
                </div>
                <div class="bm-field">
                    <label for="unit-capacity">Capacity</label>
                    <input id="unit-capacity" name="capacity" type="number" min="1" required>
                </div>
                <div class="bm-field">
                    <label for="unit-quantity">Quantity</label>
                    <input id="unit-quantity" name="quantity" type="number" min="1" value="1" required>
                </div>
                <div class="bm-field">
                    <label for="unit-price">Price override</label>
                    <input id="unit-price" name="price_override" type="number" min="0" step="0.01">
                </div>
            </div>
            <button type="submit" class="bm-btn secondary">Add {{ $listing->isRestaurant() ? 'table' : 'unit' }}</button>
        </form>
    </div>

    @if($listing->status === 'draft')
        <form method="POST" action="{{ route('owner.listings.publish', $listing) }}">
            @csrf
            <button type="submit" class="bm-btn success full">Submit for review</button>
        </form>
    @endif

    <form method="POST" action="{{ route('owner.listings.destroy', $listing) }}" onsubmit="return confirm('Delete this listing permanently?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="bm-btn danger full">Delete listing</button>
    </form>
</x-dashboard-layout>

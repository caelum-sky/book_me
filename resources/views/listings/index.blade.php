<x-public-layout title="Browse Listings">
    @php
        $heroListings = $listings->take(3);
    @endphp

    <section class="bm-landing-hero" aria-labelledby="browse-heading">
        <div class="bm-hero-copy">
            <h1 id="browse-heading">Find stays and tables that fit the trip.</h1>
            <p>Dorms, houses, pads, hotels, inns, motels, and restaurants in one searchable place.</p>
        </div>

        <div class="bm-hero-visual" aria-hidden="true">
            @forelse($heroListings as $listing)
                <div class="bm-hero-shot">
                    @if($listing->primaryImage())
                        <img src="{{ $listing->primaryImage()->url() }}" alt="">
                    @else
                        <div class="fallback">No photo yet</div>
                    @endif
                    <div class="caption">{{ $listing->title }}</div>
                </div>
            @empty
                <div class="bm-hero-shot">
                    <div class="fallback">Listings will appear here once owners publish them.</div>
                </div>
                <div class="bm-hero-shot">
                    <div class="fallback">Search by city, guests, and budget.</div>
                </div>
                <div class="bm-hero-shot">
                    <div class="fallback">BookMe keeps rooms and restaurants together.</div>
                </div>
            @endforelse
        </div>
    </section>

    <form method="GET" action="{{ route('listings.index') }}" class="bm-filter-card">
        <div class="bm-filter-grid">
            <div class="bm-filter-field">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
                <input class="bm-filter-input" type="text" name="city" placeholder="Where are you going?" value="{{ request('city') }}" maxlength="100">
            </div>

            <input class="bm-filter-input" type="number" name="guests" placeholder="Guests" min="1" max="100" value="{{ request('guests') }}">

            <input class="bm-filter-input" type="number" name="max_price" placeholder="Max price" min="0" step="0.01" value="{{ request('max_price') }}">

            @if(request('type'))
                <input type="hidden" name="type" value="{{ request('type') }}">
            @endif

            <button type="submit" class="bm-btn primary" style="border-radius:999px;">Search</button>
        </div>

        <div class="bm-tab-row" style="margin-top:12px;">
            <a href="{{ route('listings.index', request()->except('type')) }}" class="bm-tab {{ !request('type') ? 'active' : '' }}">All</a>
            @foreach($types as $type)
                <a href="{{ route('listings.index', array_merge(request()->except('type'), ['type' => $type])) }}"
                    class="bm-tab {{ request('type') === $type ? 'active' : '' }}">{{ ucfirst($type) }}</a>
            @endforeach
        </div>
    </form>

    @if($listings->isEmpty())
        <div class="bm-empty" style="padding:80px 20px;">
            <div class="icon">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin:0 auto;"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
            </div>
            No listings match your search. Try adjusting your filters.
        </div>
    @else
        <div class="bm-listing-grid">
            @foreach($listings as $listing)
                <a href="{{ route('listings.show', $listing) }}" class="bm-listing-card">
                    <div class="bm-listing-media">
                        @if($listing->primaryImage())
                            <img src="{{ $listing->primaryImage()->url() }}" alt="{{ $listing->title }}">
                        @else
                            <div class="bm-empty" style="height:100%;display:flex;align-items:center;justify-content:center;padding:0;">No photo yet</div>
                        @endif

                        @if($listing->gallery()->count() > 1)
                            <div class="bm-listing-dots">
                                @foreach($listing->gallery()->take(5) as $img)
                                    <span></span>
                                @endforeach
                            </div>
                        @endif

                        <div class="bm-type-pill">{{ $listing->type }}</div>
                    </div>

                    <div class="bm-listing-title-row">
                        <div class="bm-listing-title">{{ $listing->title }}</div>
                        @if($listing->reviews_count > 0)
                            <div class="bm-listing-rating">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01z"/></svg>
                                {{ number_format($listing->average_rating, 1) }}
                            </div>
                        @endif
                    </div>
                    <div class="bm-listing-meta">{{ $listing->city ?? $listing->business->city }}</div>
                    <div class="bm-listing-price">
                        <strong>&#8369;{{ number_format($listing->base_price, 0) }}</strong>
                        <span>/ {{ str_replace('per_', '', $listing->price_unit) }}</span>
                    </div>
                </a>
            @endforeach
        </div>

        <div style="margin-top:32px;">
            {{ $listings->links() }}
        </div>
    @endif
</x-public-layout>

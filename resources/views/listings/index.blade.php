<x-public-layout title="Browse Listings">

    <h1 style="font-size:26px;font-weight:700;margin:18px 0 4px;">Find your next stay or table</h1>
    <p style="color:var(--text-secondary);font-size:13.5px;margin-bottom:22px;">Dorms, houses, pads, hotels, inns, motels, and restaurants — all in one place.</p>

    <form method="GET" action="{{ route('listings.index') }}" style="margin-bottom:20px;">
        <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
            <div style="position:relative;flex:1;min-width:220px;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--text-tertiary);"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
                <input type="text" name="city" placeholder="Where are you going?" value="{{ request('city') }}"
                    style="width:100%;background:var(--panel-2);border:1px solid var(--border-soft);border-radius:999px;padding:12px 16px 12px 38px;color:var(--text-primary);font-size:13.5px;">
            </div>

            <input type="number" name="guests" placeholder="Guests" min="1" value="{{ request('guests') }}"
                style="width:100px;background:var(--panel-2);border:1px solid var(--border-soft);border-radius:999px;padding:12px 16px;color:var(--text-primary);font-size:13px;">

            <input type="number" name="max_price" placeholder="Max ₱" min="0" value="{{ request('max_price') }}"
                style="width:120px;background:var(--panel-2);border:1px solid var(--border-soft);border-radius:999px;padding:12px 16px;color:var(--text-primary);font-size:13px;">

            @if(request('type'))
                <input type="hidden" name="type" value="{{ request('type') }}">
            @endif

            <button type="submit" class="bm-btn primary" style="border-radius:999px;padding:12px 24px;">Search</button>
        </div>

        <div style="display:flex;gap:8px;margin-top:12px;flex-wrap:wrap;">
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
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:22px;">
            @foreach($listings as $listing)
                <a href="{{ route('listings.show', $listing) }}" style="text-decoration:none;color:inherit;display:block;">
                    <div style="aspect-ratio:1.1;border-radius:14px;overflow:hidden;background:var(--panel-2);position:relative;margin-bottom:10px;">
                        @if($listing->primaryImage())
                            <img src="{{ $listing->primaryImage()->url() }}" alt="{{ $listing->title }}"
                                style="width:100%;height:100%;object-fit:cover;transition:transform 0.3s ease;"
                                onmouseover="this.style.transform='scale(1.04)'" onmouseout="this.style.transform='scale(1)'">
                        @else
                            <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:var(--text-tertiary);font-size:13px;">No photo yet</div>
                        @endif

                        @if($listing->images->count() > 1)
                            <div style="position:absolute;bottom:10px;left:0;right:0;display:flex;justify-content:center;gap:4px;">
                                @foreach($listing->images->take(5) as $i => $img)
                                    <span style="width:5px;height:5px;border-radius:50%;background:{{ $i === 0 ? 'white' : 'rgba(255,255,255,0.5)' }};"></span>
                                @endforeach
                            </div>
                        @endif

                        <div style="position:absolute;top:10px;left:10px;background:rgba(0,0,0,0.55);backdrop-filter:blur(4px);color:white;font-size:11px;font-weight:600;padding:4px 10px;border-radius:999px;text-transform:capitalize;">
                            {{ $listing->type }}
                        </div>
                    </div>

                    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:8px;">
                        <div style="font-size:14px;font-weight:600;line-height:1.3;">{{ $listing->title }}</div>
                        @if($listing->reviews_count > 0)
                            <div style="display:flex;align-items:center;gap:3px;font-size:13px;flex-shrink:0;">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01z"/></svg>
                                {{ number_format($listing->average_rating, 1) }}
                            </div>
                        @endif
                    </div>
                    <div style="font-size:13px;color:var(--text-tertiary);margin-top:2px;">{{ $listing->city ?? $listing->business->city }}</div>
                    <div style="font-size:13.5px;margin-top:4px;">
                        <strong>₱{{ number_format($listing->base_price, 0) }}</strong>
                        <span style="color:var(--text-tertiary);">/ {{ str_replace('per_', '', $listing->price_unit) }}</span>
                    </div>
                </a>
            @endforeach
        </div>

        <div style="margin-top:32px;">
            {{ $listings->links() }}
        </div>
    @endif

</x-public-layout>
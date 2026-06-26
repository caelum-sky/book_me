<x-dashboard-layout title="Review Listing" active="listings" subheading="{{ $listing->title }}">

    <div class="bm-form-card">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:18px;">
            <div>
                <a href="{{ route('admin.listings.index') }}" style="font-size:12.5px;color:var(--text-tertiary);text-decoration:none;">&larr; Back to listings</a>
                <h2 style="font-size:19px;font-weight:700;margin:8px 0 4px;">{{ $listing->title }}</h2>
                <div style="font-size:13px;color:var(--text-secondary);">
                    {{ ucfirst($listing->type) }} &middot; {{ $listing->business->name }} &middot; {{ $listing->business->owner->name }}
                </div>
            </div>
            <div class="bm-row-badge
                @if($listing->status === 'published') badge-green
                @elseif($listing->status === 'pending_review') badge-amber
                @elseif($listing->status === 'draft') badge-gray
                @else badge-red
                @endif" style="flex-shrink:0;">
                {{ ucfirst(str_replace('_', ' ', $listing->status)) }}
            </div>
        </div>

        @if($listing->images->isNotEmpty())
            <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:8px;margin-bottom:18px;">
                @foreach($listing->images->take(4) as $image)
                    <div style="aspect-ratio:1;border-radius:10px;overflow:hidden;background:var(--panel);">
                        <img src="{{ $image->url() }}" style="width:100%;height:100%;object-fit:cover;">
                    </div>
                @endforeach
            </div>
        @endif

        @if($listing->description)
            <p style="font-size:13.5px;color:var(--text-secondary);line-height:1.6;margin-bottom:18px;">{{ $listing->description }}</p>
        @endif

        <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;font-size:13px;margin-bottom:18px;">
            <div>
                <div style="color:var(--text-tertiary);margin-bottom:2px;">Base price</div>
                <div style="font-weight:600;">₱{{ number_format($listing->base_price, 0) }} / {{ str_replace('per_', '', $listing->price_unit) }}</div>
            </div>
            @if($listing->city)
                <div>
                    <div style="color:var(--text-tertiary);margin-bottom:2px;">Location</div>
                    <div style="font-weight:600;">{{ $listing->city }}{{ $listing->province ? ', '.$listing->province : '' }}</div>
                </div>
            @endif
            @if($listing->max_guests)
                <div>
                    <div style="color:var(--text-tertiary);margin-bottom:2px;">Max guests</div>
                    <div style="font-weight:600;">{{ $listing->max_guests }}</div>
                </div>
            @endif
            @if($listing->seating_capacity)
                <div>
                    <div style="color:var(--text-tertiary);margin-bottom:2px;">Seating capacity</div>
                    <div style="font-weight:600;">{{ $listing->seating_capacity }}</div>
                </div>
            @endif
        </div>

        @if($listing->units->isNotEmpty())
            <div style="border-top:1px solid var(--border);padding-top:14px;margin-bottom:18px;">
                <div style="font-size:13px;font-weight:600;margin-bottom:8px;">Units ({{ $listing->units->count() }})</div>
                @foreach($listing->units as $unit)
                    <div style="font-size:13px;color:var(--text-secondary);padding:4px 0;">{{ $unit->name }} &middot; capacity {{ $unit->capacity }} &middot; qty {{ $unit->quantity }}</div>
                @endforeach
            </div>
        @endif

        @if($listing->status === 'pending_review')
            <div style="border-top:1px solid var(--border);padding-top:18px;display:flex;gap:10px;">
                <form method="POST" action="{{ route('admin.listings.approve', $listing) }}" style="flex:1;">
                    @csrf
                    <button type="submit" class="bm-btn success full">Approve & publish</button>
                </form>
                <button type="button" onclick="document.getElementById('reject-panel').classList.toggle('hidden')" class="bm-btn danger full" style="flex:1;">
                    Reject
                </button>
            </div>
            <form id="reject-panel" method="POST" action="{{ route('admin.listings.reject', $listing) }}" class="hidden" style="margin-top:12px;">
                @csrf
                <div class="bm-field">
                    <input name="rejection_reason" type="text" placeholder="Reason for rejection" required>
                </div>
                <button type="submit" class="bm-btn danger full">Confirm rejection</button>
            </form>
        @endif
    </div>

</x-dashboard-layout>
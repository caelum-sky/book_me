<x-dashboard-layout title="Listing Approvals" active="listings" subheading="Review submitted dorms, hotels, restaurants, and more">

    <div class="bm-tab-row">
        @foreach(['pending_review' => 'Pending', 'published' => 'Published', 'rejected' => 'Rejected', 'suspended' => 'Suspended', 'all' => 'All'] as $value => $label)
            <a href="{{ route('admin.listings.index', ['status' => $value]) }}" class="bm-tab {{ $status === $value ? 'active' : '' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <div class="bm-list-card">
        @forelse($listings as $listing)
            <div class="bm-list-row" style="align-items:flex-start;cursor:default;">
                <div class="bm-row-avatar" style="background:linear-gradient(135deg,#7c5cff,#4f9bff);">
                    {{ strtoupper(substr($listing->type, 0, 2)) }}
                </div>
                <div class="bm-row-info">
                    <div class="name">{{ $listing->title }}</div>
                    <div class="meta">{{ ucfirst($listing->type) }} &middot; {{ $listing->business->name }} by {{ $listing->business->owner->name }}</div>
                    @if($listing->city)
                        <div class="meta">{{ $listing->city }}</div>
                    @endif
                    @if($listing->rejection_reason)
                        <div class="meta" style="color:#f87171;margin-top:4px;">{{ $listing->rejection_reason }}</div>
                    @endif

                    <div style="margin-top:10px;">
                        <a href="{{ route('admin.listings.show', $listing) }}" style="font-size:12.5px;color:#a08bff;text-decoration:none;font-weight:600;">View details &rarr;</a>
                    </div>

                    @if($listing->status === 'pending_review')
                        <div style="display:flex;gap:8px;margin-top:12px;">
                            <form method="POST" action="{{ route('admin.listings.approve', $listing) }}">
                                @csrf
                                <button type="submit" class="bm-btn success sm">Approve & publish</button>
                            </form>
                            <button type="button" onclick="document.getElementById('reject-listing-{{ $listing->id }}').classList.toggle('hidden')" class="bm-btn danger sm">Reject</button>
                        </div>
                        <form id="reject-listing-{{ $listing->id }}" method="POST" action="{{ route('admin.listings.reject', $listing) }}" class="hidden" style="margin-top:10px;display:flex;gap:8px;max-width:420px;">
                            @csrf
                            <input name="rejection_reason" type="text" placeholder="Reason for rejection" required
                                style="flex:1;background:var(--panel);border:1px solid var(--border-soft);border-radius:10px;padding:9px 12px;color:var(--text-primary);font-size:13px;">
                            <button type="submit" class="bm-btn danger sm">Confirm</button>
                        </form>
                    @elseif($listing->status === 'published')
                        <form method="POST" action="{{ route('admin.listings.suspend', $listing) }}" style="margin-top:12px;"
                            onsubmit="return confirm('Suspend this listing?');">
                            @csrf
                            <button type="submit" class="bm-btn secondary sm">Suspend</button>
                        </form>
                    @endif
                </div>
                <div class="bm-row-badge
                    @if($listing->status === 'published') badge-green
                    @elseif($listing->status === 'pending_review') badge-amber
                    @elseif($listing->status === 'draft') badge-gray
                    @else badge-red
                    @endif">
                    {{ ucfirst(str_replace('_', ' ', $listing->status)) }}
                </div>
            </div>
        @empty
            <div class="bm-empty">No listings with this status.</div>
        @endforelse
    </div>

    <div>{{ $listings->links() }}</div>

</x-dashboard-layout>
<x-dashboard-layout title="Review Business" active="businesses" subheading="{{ $business->name }}">

    <a href="{{ route('admin.businesses.index') }}" style="font-size:12.5px;color:var(--text-tertiary);text-decoration:none;">&larr; Back to businesses</a>

    <div class="bm-form-card" style="margin-top:14px;">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:18px;">
            <div>
                <h2 style="font-size:19px;font-weight:700;margin:0 0 4px;">{{ $business->name }}</h2>
                <div style="font-size:13px;color:var(--text-secondary);">
                    {{ $business->owner->name }} &middot; {{ $business->owner->email }}
                    @if($business->owner->phone)
                        &middot; {{ $business->owner->phone }}
                    @endif
                </div>
            </div>
            <div class="bm-row-badge
                @if($business->status === 'approved') badge-green
                @elseif($business->status === 'pending') badge-amber
                @else badge-red
                @endif" style="flex-shrink:0;">
                {{ ucfirst($business->status) }}
            </div>
        </div>

        @if($business->description)
            <p style="font-size:13.5px;color:var(--text-secondary);line-height:1.6;margin-bottom:18px;">{{ $business->description }}</p>
        @endif

        <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;font-size:13px;margin-bottom:18px;">
            @if($business->contact_email)
                <div>
                    <div style="color:var(--text-tertiary);margin-bottom:2px;">Contact email</div>
                    <div style="font-weight:600;">{{ $business->contact_email }}</div>
                </div>
            @endif
            @if($business->contact_phone)
                <div>
                    <div style="color:var(--text-tertiary);margin-bottom:2px;">Contact phone</div>
                    <div style="font-weight:600;">{{ $business->contact_phone }}</div>
                </div>
            @endif
            @if($business->address)
                <div>
                    <div style="color:var(--text-tertiary);margin-bottom:2px;">Address</div>
                    <div style="font-weight:600;">{{ $business->address }}</div>
                </div>
            @endif
            @if($business->city)
                <div>
                    <div style="color:var(--text-tertiary);margin-bottom:2px;">Location</div>
                    <div style="font-weight:600;">{{ $business->city }}{{ $business->province ? ', '.$business->province : '' }}</div>
                </div>
            @endif
        </div>

        @if($business->rejection_reason)
            <div class="bm-alert error" style="margin-bottom:18px;">
                <span><strong>Rejection reason:</strong> {{ $business->rejection_reason }}</span>
            </div>
        @endif

        @if($business->approved_at)
            <div style="font-size:12.5px;color:var(--text-tertiary);margin-bottom:18px;">
                Approved {{ $business->approved_at->diffForHumans() }}
            </div>
        @endif

        @if($business->status === 'pending')
            <div style="border-top:1px solid var(--border);padding-top:18px;display:flex;gap:10px;">
                <form method="POST" action="{{ route('admin.businesses.approve', $business) }}" style="flex:1;">
                    @csrf
                    <button type="submit" class="bm-btn success full">Approve</button>
                </form>
                <button type="button" onclick="document.getElementById('reject-panel').classList.toggle('hidden')" class="bm-btn danger full" style="flex:1;">
                    Reject
                </button>
            </div>
            <form id="reject-panel" method="POST" action="{{ route('admin.businesses.reject', $business) }}" class="hidden" style="margin-top:12px;">
                @csrf
                <div class="bm-field">
                    <input name="rejection_reason" type="text" placeholder="Reason for rejection" required>
                </div>
                <button type="submit" class="bm-btn danger full">Confirm rejection</button>
            </form>
        @elseif($business->status === 'approved')
            <div style="border-top:1px solid var(--border);padding-top:18px;">
                <form method="POST" action="{{ route('admin.businesses.suspend', $business) }}" onsubmit="return confirm('Suspend this business? The owner will lose access to manage listings and bookings.');">
                    @csrf
                    <button type="submit" class="bm-btn secondary full">Suspend business</button>
                </form>
            </div>
        @endif
    </div>

    <div class="bm-list-card" style="margin-top:16px;">
        <div class="bm-list-head">
            <h2>Listings ({{ $business->listings->count() }})</h2>
        </div>

        @forelse($business->listings as $listing)
            <a href="{{ route('admin.listings.show', $listing) }}" class="bm-list-row">
                <div class="bm-row-avatar" style="background:linear-gradient(135deg,#7c5cff,#4f9bff);">
                    {{ strtoupper(substr($listing->type, 0, 2)) }}
                </div>
                <div class="bm-row-info">
                    <div class="name">{{ $listing->title }}</div>
                    <div class="meta">{{ ucfirst($listing->type) }} &middot; ₱{{ number_format($listing->base_price, 0) }}</div>
                </div>
                <div class="bm-row-badge
                    @if($listing->status === 'published') badge-green
                    @elseif($listing->status === 'pending_review') badge-amber
                    @elseif($listing->status === 'draft') badge-gray
                    @else badge-red
                    @endif">
                    {{ ucfirst(str_replace('_', ' ', $listing->status)) }}
                </div>a
            </a>
        @empty
            <div class="bm-empty">This business hasn't created any listings yet.</div>
        @endforelse
    </div>

</x-dashboard-layout>
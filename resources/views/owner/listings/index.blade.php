<x-dashboard-layout title="My Listings" active="listings" subheading="Manage your dorms, hotels, dining, and more">

    <div style="display:flex;justify-content:flex-end;margin-bottom:18px;">
        <button type="button" class="bm-btn primary" onclick="bmOpenModal('modal-new-listing')">+ New listing</button>
    </div>

    <div class="bm-list-card">
        @forelse($listings as $listing)
            <div class="bm-list-row">
                <div class="bm-row-avatar" style="background:var(--panel);overflow:hidden;">
                    @if($listing->primaryImage())
                        <img src="{{ $listing->primaryImage()->url() }}" style="width:100%;height:100%;object-fit:cover;">
                    @else
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9.5 12 3l9 6.5V20a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1Z"/></svg>
                    @endif
                </div>
                <div class="bm-row-info">
                    <div class="name">{{ $listing->title }}</div>
                    <div class="meta">{{ ucfirst($listing->type) }} &middot; {{ $listing->bookings_count }} bookings</div>
                </div>
                <div class="bm-row-badge
                    @if($listing->status === 'published') badge-green
                    @elseif($listing->status === 'pending_review') badge-amber
                    @elseif($listing->status === 'draft') badge-gray
                    @else badge-red
                    @endif">
                    {{ ucfirst(str_replace('_', ' ', $listing->status)) }}
                </div>
                <button type="button" class="bm-btn secondary sm"
                    onclick="bmOpenModal('modal-calendar-{{ $listing->id }}')">Calendar</button>
                <button type="button" class="bm-btn primary sm"
                    onclick="bmOpenModal('modal-edit-{{ $listing->id }}')">Edit</button>
            </div>
        @empty
            <div class="bm-empty">
                <div style="margin-bottom:14px;">You haven't created any listings yet.</div>
                <button type="button" class="bm-btn primary" onclick="bmOpenModal('modal-new-listing')">
                    Create your first listing
                </button>
            </div>
        @endforelse
    </div>

    <div style="margin-top:18px;">{{ $listings->links() }}</div>

    {{--
        All modal HTML lives in a separate partial so it is clearly separated
        from the page content. The dialogs are rendered into the DOM but are
        NOT open — they only become visible when bmOpenModal() is called.
        They are never auto-opened because:
          1. No <dialog> has the `open` attribute in this template.
          2. bm-modal.js force-closes any dialog that bfcache restores as open.
          3. bmOpenModal() closes all others before opening the target.
    --}}
    @include('owner.listings._modals', ['listings' => $listings, 'types' => $types])

</x-dashboard-layout>
<x-dashboard-layout title="Listing Approvals" active="listings" subheading="Review submitted dorms, hotels, restaurants, and more">

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;flex-wrap:wrap;gap:10px;">
        <div class="bm-tab-row">
            @foreach(['pending_review' => 'Pending', 'published' => 'Published', 'rejected' => 'Rejected', 'suspended' => 'Suspended', 'all' => 'All'] as $value => $label)
                <a href="{{ route('admin.listings.index', ['status' => $value]) }}" class="bm-tab {{ $status === $value ? 'active' : '' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
        <button type="button" class="bm-btn primary" onclick="bmOpenModal('modal-new-listing')">+ New listing</button>
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

                    <div style="display:flex;gap:8px;margin-top:12px;flex-wrap:wrap;">
                        <a href="{{ route('admin.listings.show', $listing) }}" style="font-size:12.5px;color:#a08bff;text-decoration:none;font-weight:600;align-self:center;">View details &rarr;</a>

                        @if($listing->status === 'pending_review')
                            <form method="POST" action="{{ route('admin.listings.approve', $listing) }}">
                                @csrf
                                <button type="submit" class="bm-btn success sm">Approve & publish</button>
                            </form>
                            <button type="button" onclick="bmOpenModal('modal-reject-listing-{{ $listing->id }}')" class="bm-btn danger sm">Reject</button>
                        @elseif($listing->status === 'published')

                        @endif

                        <button type="button" onclick="bmOpenModal('modal-edit-listing-{{ $listing->id }}')" class="bm-btn secondary sm">Edit</button>
                        <button type="button" onclick="bmOpenModal('modal-delete-listing-{{ $listing->id }}')" class="bm-btn danger sm">Delete</button>
                    </div>
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

    <div style="margin-top:18px;">{{ $listings->links() }}</div>

    {{-- ─── NEW LISTING MODAL ──────────────────────────────────────────── --}}
    <dialog id="modal-new-listing" class="bm-modal lg">
        <div class="bm-modal-head">
            <h2>New listing</h2>
            <button type="button" class="bm-modal-close" onclick="bmCloseModal('modal-new-listing')">&times;</button>
        </div>
        <form method="POST" action="{{ route('admin.listings.store') }}" enctype="multipart/form-data" style="display:flex;flex-direction:column;flex:1;min-height:0;">
            @csrf
            <div class="bm-modal-body">
                @if($businesses->isEmpty())
                    <div class="bm-alert error">
                        <span>No approved businesses exist yet. Approve a business first before creating listings for it.</span>
                    </div>
                @else
                    <div class="bm-field">
                        <label>Business</label>
                        <select name="business_id" required>
                            <option value="">Select a business</option>
                            @foreach($businesses as $biz)
                                <option value="{{ $biz->id }}">{{ $biz->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                @include('admin.listings._form', ['listing' => null])

                <div class="bm-field">
                    <label>Status</label>
                    <select name="status">
                        <option value="draft">Draft</option>
                        <option value="pending_review">Pending review</option>
                        <option value="published">Published</option>
                    </select>
                </div>
            </div>
            <div class="bm-modal-foot">
                <button type="button" class="bm-btn secondary" onclick="bmCloseModal('modal-new-listing')">Cancel</button>
                <button type="submit" class="bm-btn primary" {{ $businesses->isEmpty() ? 'disabled' : '' }}>Create listing</button>
            </div>
        </form>
    </dialog>

    {{-- ─── PER-LISTING MODALS ─────────────────────────────────────────── --}}
    @foreach($listings as $listing)

        {{-- Edit --}}
        <dialog id="modal-edit-listing-{{ $listing->id }}" class="bm-modal lg">
            <div class="bm-modal-head">
                <h2>Edit — {{ $listing->title }}</h2>
                <button type="button" class="bm-modal-close" onclick="bmCloseModal('modal-edit-listing-{{ $listing->id }}')">&times;</button>
            </div>
            <form method="POST" action="{{ route('admin.listings.update', $listing) }}" enctype="multipart/form-data" style="display:flex;flex-direction:column;flex:1;min-height:0;">
                @csrf
                @method('PUT')
                <div class="bm-modal-body">

                    @include('admin.listings._form', ['listing' => $listing])

                    <div class="bm-field">
                        <label>Status</label>
                        <select name="status">
                            @foreach(['draft', 'pending_review', 'published', 'rejected', 'suspended'] as $s)
                                <option value="{{ $s }}" {{ $listing->status === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
                            @endforeach
                        </select>
                    </div>

                    @if($listing->gallery()->isNotEmpty())
                        <div style="margin-top:18px;">
                            <label style="display:block;font-size:13px;font-weight:600;color:var(--text-secondary);margin-bottom:8px;">Current photos</label>
                            <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:8px;">
                                @foreach($listing->gallery() as $image)
                                    <div style="aspect-ratio:1;border-radius:8px;overflow:hidden;background:var(--panel);">
                                        <img src="{{ $image->url() }}" style="width:100%;height:100%;object-fit:cover;">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div style="border-top:1px solid var(--border);margin-top:20px;padding-top:18px;">
                        <label style="display:block;font-size:13px;font-weight:600;color:var(--text-secondary);margin-bottom:8px;">
                            {{ $listing->isRestaurant() ? 'Tables' : 'Rooms / Units' }}
                        </label>
                        @foreach($listing->units as $unit)
                            <div class="bm-list-row" style="padding:10px 12px;margin-bottom:6px;">
                                <div class="bm-row-info">
                                    <div class="name" style="font-size:13px;">{{ $unit->name }}</div>
                                    <div class="meta">Cap {{ $unit->capacity }} &middot; Qty {{ $unit->quantity }}</div>
                                </div>
                                <button type="button"
                                    onclick="if(confirm('Remove this unit?')) document.getElementById('admin-rm-unit-{{ $unit->id }}').submit()"
                                    class="bm-btn danger sm">Remove</button>
                            </div>
                        @endforeach
                        <button type="button" class="bm-btn secondary sm" onclick="bmOpenModal('modal-admin-add-unit-{{ $listing->id }}')">
                            + Add {{ $listing->isRestaurant() ? 'table' : 'unit' }}
                        </button>
                    </div>

                </div>
                <div class="bm-modal-foot">
                    <button type="button" class="bm-btn secondary" onclick="bmCloseModal('modal-edit-listing-{{ $listing->id }}')">Cancel</button>
                    <button type="submit" class="bm-btn primary">Save changes</button>
                </div>
            </form>
        </dialog>

        {{-- Add unit --}}
        <dialog id="modal-admin-add-unit-{{ $listing->id }}" class="bm-modal">
            <div class="bm-modal-head">
                <h2>Add {{ $listing->isRestaurant() ? 'table' : 'unit' }}</h2>
                <button type="button" class="bm-modal-close" onclick="bmOpenModal('modal-edit-listing-{{ $listing->id }}')">&times;</button>
            </div>
            <form method="POST" action="{{ route('admin.listings.units.store', $listing) }}">
                @csrf
                <div class="bm-modal-body">
                    <div class="bm-field">
                        <label>{{ $listing->isRestaurant() ? 'Table name' : 'Room name' }}</label>
                        <input name="name" type="text" required placeholder="e.g. Room 101">
                    </div>
                    <div class="bm-field-row">
                        <div class="bm-field">
                            <label>Capacity</label>
                            <input name="capacity" type="number" min="1" required>
                        </div>
                        <div class="bm-field">
                            <label>Quantity</label>
                            <input name="quantity" type="number" min="1" value="1" required>
                        </div>
                    </div>
                    <div class="bm-field">
                        <label>Price override <span class="hint">(optional)</span></label>
                        <input name="price_override" type="number" min="0" step="0.01">
                    </div>
                </div>
                <div class="bm-modal-foot">
                    <button type="button" class="bm-btn secondary" onclick="bmOpenModal('modal-edit-listing-{{ $listing->id }}')">Cancel</button>
                    <button type="submit" class="bm-btn primary">Add</button>
                </div>
            </form>
        </dialog>

        @foreach($listing->units as $unit)
            <form id="admin-rm-unit-{{ $unit->id }}" method="POST" action="{{ route('admin.listings.units.destroy', [$listing, $unit]) }}" style="display:none;">
                @csrf @method('DELETE')
            </form>
        @endforeach

        {{-- Reject reason --}}
        @if($listing->status === 'pending_review')
            <dialog id="modal-reject-listing-{{ $listing->id }}" class="bm-modal">
                <div class="bm-modal-head">
                    <h2>Reject listing</h2>
                    <button type="button" class="bm-modal-close" onclick="bmCloseModal('modal-reject-listing-{{ $listing->id }}')">&times;</button>
                </div>
                <form method="POST" action="{{ route('admin.listings.reject', $listing) }}">
                    @csrf
                    <div class="bm-modal-body">
                        <div class="bm-field">
                            <label>Reason for rejection</label>
                            <input name="rejection_reason" type="text" required>
                        </div>
                    </div>
                    <div class="bm-modal-foot">
                        <button type="button" class="bm-btn secondary" onclick="bmCloseModal('modal-reject-listing-{{ $listing->id }}')">Cancel</button>
                        <button type="submit" class="bm-btn danger">Confirm rejection</button>
                    </div>
                </form>
            </dialog>
        @endif

        {{-- Delete confirmation --}}
        <dialog id="modal-delete-listing-{{ $listing->id }}" class="bm-modal">
            <div class="bm-modal-head">
                <h2>Delete listing</h2>
                <button type="button" class="bm-modal-close" onclick="bmCloseModal('modal-delete-listing-{{ $listing->id }}')">&times;</button>
            </div>
            <div class="bm-modal-body">
                <p style="font-size:13.5px;color:var(--text-secondary);">
                    Permanently delete <strong style="color:var(--text-primary);">{{ $listing->title }}</strong>? This cannot be undone.
                </p>
            </div>
            <div class="bm-modal-foot">
                <button type="button" class="bm-btn secondary" onclick="bmCloseModal('modal-delete-listing-{{ $listing->id }}')">Cancel</button>
                <form method="POST" action="{{ route('admin.listings.destroy', $listing) }}">
                    @csrf @method('DELETE')
                    <button type="submit" class="bm-btn danger">Delete permanently</button>
                </form>
            </div>
        </dialog>

    @endforeach

</x-dashboard-layout>
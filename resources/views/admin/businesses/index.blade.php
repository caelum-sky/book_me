<x-dashboard-layout title="Business Approvals" active="businesses" subheading="Review and manage vendor accounts">

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;flex-wrap:wrap;gap:10px;">
        <div class="bm-tab-row">
            @foreach(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', 'suspended' => 'Suspended', 'all' => 'All'] as $value => $label)
                <a href="{{ route('admin.businesses.index', ['status' => $value]) }}" class="bm-tab {{ $status === $value ? 'active' : '' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
        <button type="button" class="bm-btn primary" onclick="bmOpenModal('modal-new-business')">+ New business</button>
    </div>

    <div class="bm-list-card">
        @forelse($businesses as $business)
            <div class="bm-list-row" style="align-items:flex-start;cursor:default;">
                <div class="bm-row-avatar" style="background:linear-gradient(135deg,#ec4faa,#7c5cff);">
                    {{ collect(explode(' ', $business->name))->map(fn($p) => mb_substr($p,0,1))->take(2)->implode('') }}
                </div>
                <div class="bm-row-info">
                    <div class="name">{{ $business->name }}</div>
                    <div class="meta">{{ $business->owner->name }} &middot; {{ $business->owner->email }}</div>
                    @if($business->city)
                        <div class="meta">{{ $business->city }}{{ $business->province ? ', '.$business->province : '' }}</div>
                    @endif
                    @if($business->rejection_reason)
                        <div class="meta" style="color:#f87171;margin-top:4px;">Reason: {{ $business->rejection_reason }}</div>
                    @endif

                    <div style="display:flex;gap:8px;margin-top:12px;flex-wrap:wrap;">
                        <a href="{{ route('admin.businesses.show', $business) }}" style="font-size:12.5px;color:#a08bff;text-decoration:none;font-weight:600;align-self:center;">View details &rarr;</a>

                        @if($business->status === 'pending')
                            <form method="POST" action="{{ route('admin.businesses.approve', $business) }}">
                                @csrf
                                <button type="submit" class="bm-btn success sm">Approve</button>
                            </form>
                            <button type="button" onclick="bmOpenModal('modal-reject-{{ $business->id }}')" class="bm-btn danger sm">Reject</button>
                        @elseif($business->status === 'approved')

                        @endif

                        <button type="button" onclick="bmOpenModal('modal-edit-business-{{ $business->id }}')" class="bm-btn secondary sm">Edit</button>
                        <button type="button" onclick="bmOpenModal('modal-delete-business-{{ $business->id }}')" class="bm-btn danger sm">Delete</button>
                    </div>
                </div>
                <div class="bm-row-badge
                    @if($business->status === 'approved') badge-green
                    @elseif($business->status === 'pending') badge-amber
                    @else badge-red
                    @endif">
                    {{ ucfirst($business->status) }}
                </div>
            </div>
        @empty
            <div class="bm-empty">No businesses with this status.</div>
        @endforelse
    </div>

    <div style="margin-top:18px;">{{ $businesses->links() }}</div>

    {{-- ─── NEW BUSINESS MODAL ─────────────────────────────────────────── --}}
    <dialog id="modal-new-business" class="bm-modal lg">
        <div class="bm-modal-head">
            <h2>New business</h2>
            <button type="button" class="bm-modal-close" onclick="bmCloseModal('modal-new-business')">&times;</button>
        </div>
        <form method="POST" action="{{ route('admin.businesses.store') }}" style="display:flex;flex-direction:column;flex:1;min-height:0;">
            @csrf
            <div class="bm-modal-body">
                @if($availableOwners->isEmpty())
                    <div class="bm-alert error">
                        <span>No business-owner accounts are currently available without an existing business. The owner must register first.</span>
                    </div>
                @else
                    <div class="bm-field">
                        <label>Owner</label>
                        <select name="owner_id" required>
                            <option value="">Select an owner</option>
                            @foreach($availableOwners as $owner)
                                <option value="{{ $owner->id }}">{{ $owner->name }} ({{ $owner->email }})</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="bm-field">
                    <label>Business name</label>
                    <input name="name" type="text" required>
                </div>
                <div class="bm-field">
                    <label>Description</label>
                    <textarea name="description" rows="3"></textarea>
                </div>
                <div class="bm-field-row">
                    <div class="bm-field">
                        <label>Contact email</label>
                        <input name="contact_email" type="email">
                    </div>
                    <div class="bm-field">
                        <label>Contact phone</label>
                        <input name="contact_phone" type="tel">
                    </div>
                </div>
                <div class="bm-field">
                    <label>Address</label>
                    <input name="address" type="text">
                </div>
                <div class="bm-field-row">
                    <div class="bm-field">
                        <label>City</label>
                        <input name="city" type="text">
                    </div>
                    <div class="bm-field">
                        <label>Province</label>
                        <input name="province" type="text">
                    </div>
                </div>
                <div class="bm-field">
                    <label>Status</label>
                    <select name="status">
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                        <option value="suspended">Suspended</option>
                    </select>
                </div>
            </div>
            <div class="bm-modal-foot">
                <button type="button" class="bm-btn secondary" onclick="bmCloseModal('modal-new-business')">Cancel</button>
                <button type="submit" class="bm-btn primary" {{ $availableOwners->isEmpty() ? 'disabled' : '' }}>Create business</button>
            </div>
        </form>
    </dialog>

    {{-- ─── PER-BUSINESS MODALS ────────────────────────────────────────── --}}
    @foreach($businesses as $business)

        {{-- Edit --}}
        <dialog id="modal-edit-business-{{ $business->id }}" class="bm-modal lg">
            <div class="bm-modal-head">
                <h2>Edit — {{ $business->name }}</h2>
                <button type="button" class="bm-modal-close" onclick="bmCloseModal('modal-edit-business-{{ $business->id }}')">&times;</button>
            </div>
            <form method="POST" action="{{ route('admin.businesses.update', $business) }}" style="display:flex;flex-direction:column;flex:1;min-height:0;">
                @csrf
                @method('PUT')
                <div class="bm-modal-body">
                    <div class="bm-field">
                        <label>Business name</label>
                        <input name="name" type="text" value="{{ $business->name }}" required>
                    </div>
                    <div class="bm-field">
                        <label>Description</label>
                        <textarea name="description" rows="3">{{ $business->description }}</textarea>
                    </div>
                    <div class="bm-field-row">
                        <div class="bm-field">
                            <label>Contact email</label>
                            <input name="contact_email" type="email" value="{{ $business->contact_email }}">
                        </div>
                        <div class="bm-field">
                            <label>Contact phone</label>
                            <input name="contact_phone" type="tel" value="{{ $business->contact_phone }}">
                        </div>
                    </div>
                    <div class="bm-field">
                        <label>Address</label>
                        <input name="address" type="text" value="{{ $business->address }}">
                    </div>
                    <div class="bm-field-row">
                        <div class="bm-field">
                            <label>City</label>
                            <input name="city" type="text" value="{{ $business->city }}">
                        </div>
                        <div class="bm-field">
                            <label>Province</label>
                            <input name="province" type="text" value="{{ $business->province }}">
                        </div>
                    </div>
                    <div class="bm-field">
                        <label>Status</label>
                        <select name="status">
                            @foreach(['pending', 'approved', 'rejected', 'suspended'] as $s)
                                <option value="{{ $s }}" {{ $business->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="bm-modal-foot">
                    <button type="button" class="bm-btn secondary" onclick="bmCloseModal('modal-edit-business-{{ $business->id }}')">Cancel</button>
                    <button type="submit" class="bm-btn primary">Save changes</button>
                </div>
            </form>
        </dialog>

        {{-- Reject reason --}}
        @if($business->status === 'pending')
            <dialog id="modal-reject-{{ $business->id }}" class="bm-modal">
                <div class="bm-modal-head">
                    <h2>Reject business</h2>
                    <button type="button" class="bm-modal-close" onclick="bmCloseModal('modal-reject-{{ $business->id }}')">&times;</button>
                </div>
                <form method="POST" action="{{ route('admin.businesses.reject', $business) }}">
                    @csrf
                    <div class="bm-modal-body">
                        <div class="bm-field">
                            <label>Reason for rejection</label>
                            <input name="rejection_reason" type="text" required>
                        </div>
                    </div>
                    <div class="bm-modal-foot">
                        <button type="button" class="bm-btn secondary" onclick="bmCloseModal('modal-reject-{{ $business->id }}')">Cancel</button>
                        <button type="submit" class="bm-btn danger">Confirm rejection</button>
                    </div>
                </form>
            </dialog>
        @endif

        {{-- Delete confirmation --}}
        <dialog id="modal-delete-business-{{ $business->id }}" class="bm-modal">
            <div class="bm-modal-head">
                <h2>Delete business</h2>
                <button type="button" class="bm-modal-close" onclick="bmCloseModal('modal-delete-business-{{ $business->id }}')">&times;</button>
            </div>
            <div class="bm-modal-body">
                <p style="font-size:13.5px;color:var(--text-secondary);">
                    Permanently delete <strong style="color:var(--text-primary);">{{ $business->name }}</strong>?
                    This also removes all of its listings. This cannot be undone.
                </p>
            </div>
            <div class="bm-modal-foot">
                <button type="button" class="bm-btn secondary" onclick="bmCloseModal('modal-delete-business-{{ $business->id }}')">Cancel</button>
                <form method="POST" action="{{ route('admin.businesses.destroy', $business) }}">
                    @csrf @method('DELETE')
                    <button type="submit" class="bm-btn danger">Delete permanently</button>
                </form>
            </div>
        </dialog>

    @endforeach

</x-dashboard-layout>
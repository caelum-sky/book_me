{{--
    resources/views/owner/listings/_modals.blade.php

    All <dialog> modals for the My Listings page.
    Included once at the bottom of index.blade.php.
    Variables expected: $listings (paginator), $types (array)

    IMPORTANT: no dialog here has the `open` attribute.
    They are opened exclusively via bmOpenModal() in bm-modal.js.

    NOTE: $listing->images is no longer valid (images is now a method,
    gallery(), backed by the polymorphic media table) - every reference
    below uses $listing->gallery() instead.
--}}

{{-- ─── NEW LISTING ─────────────────────────────────────────────────────────── --}}
<dialog id="modal-new-listing" class="bm-modal lg">
    <div class="bm-modal-head">
        <h2>New listing</h2>
        <button type="button" class="bm-modal-close" onclick="bmCloseModal('modal-new-listing')">&times;</button>
    </div>
    <form method="POST" action="{{ route('owner.listings.store') }}" enctype="multipart/form-data"
        style="display:flex;flex-direction:column;flex:1;min-height:0;">
        @csrf
        <div class="bm-modal-body">
            @include('owner.listings._form', ['listing' => null, 'types' => $types])
        </div>
        <div class="bm-modal-foot">
            <button type="button" class="bm-btn secondary" onclick="bmCloseModal('modal-new-listing')">Cancel</button>
            <button type="submit" class="bm-btn primary">Create listing</button>
        </div>
    </form>
</dialog>

{{-- ─── PER-LISTING MODALS ──────────────────────────────────────────────────── --}}
@foreach($listings as $listing)

    {{-- ── EDIT ── --}}
    <dialog id="modal-edit-{{ $listing->id }}" class="bm-modal lg">
        <div class="bm-modal-head">
            <h2>Edit — {{ $listing->title }}</h2>
            <button type="button" class="bm-modal-close"
                onclick="bmCloseModal('modal-edit-{{ $listing->id }}')">&times;</button>
        </div>
        <form method="POST" action="{{ route('owner.listings.update', $listing) }}" enctype="multipart/form-data"
            style="display:flex;flex-direction:column;flex:1;min-height:0;">
            @csrf
            @method('PUT')
            <div class="bm-modal-body">

                @include('owner.listings._form', ['listing' => $listing, 'types' => $types])

                {{-- Existing photos --}}
                @if($listing->gallery()->isNotEmpty())
                    <div style="margin-top:18px;">
                        <label style="display:block;font-size:13px;font-weight:600;color:var(--text-secondary);margin-bottom:8px;">
                            Current photos
                        </label>
                        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:8px;">
                            @foreach($listing->gallery() as $image)
                                <div style="position:relative;aspect-ratio:1;border-radius:8px;overflow:hidden;background:var(--panel);">
                                    <img src="{{ $image->url() }}" style="width:100%;height:100%;object-fit:cover;">
                                    <button type="button"
                                        onclick="if(confirm('Remove this photo?')) document.getElementById('rm-img-{{ $image->id }}').submit()"
                                        style="position:absolute;top:3px;right:3px;background:rgba(220,38,38,0.9);color:white;border:none;width:20px;height:20px;border-radius:50%;font-size:11px;cursor:pointer;">&times;</button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Units --}}
                <div style="border-top:1px solid var(--border);margin-top:20px;padding-top:18px;">
                    <label style="display:block;font-size:13px;font-weight:600;color:var(--text-secondary);margin-bottom:8px;">
                        {{ $listing->isRestaurant() ? 'Tables' : 'Rooms / Units' }}
                    </label>
                    @foreach($listing->units as $unit)
                        <div class="bm-list-row" style="padding:10px 12px;margin-bottom:6px;">
                            <div class="bm-row-info">
                                <div class="name" style="font-size:13px;">{{ $unit->name }}</div>
                                <div class="meta">
                                    Cap {{ $unit->capacity }} &middot; Qty {{ $unit->quantity }}
                                    @if($unit->price_override)
                                        &middot; ₱{{ number_format($unit->price_override, 0) }} override
                                    @endif
                                </div>
                            </div>
                            <button type="button"
                                onclick="if(confirm('Remove this unit?')) document.getElementById('rm-unit-{{ $unit->id }}').submit()"
                                class="bm-btn danger sm">Remove</button>
                        </div>
                    @endforeach
                    <button type="button" class="bm-btn secondary sm"
                        onclick="bmOpenModal('modal-units-{{ $listing->id }}')">
                        + Add {{ $listing->isRestaurant() ? 'table' : 'unit' }}
                    </button>
                </div>

            </div>
            <div class="bm-modal-foot">
                @if($listing->status === 'draft')
                    <button type="submit" form="publish-{{ $listing->id }}" class="bm-btn success">
                        Submit for review
                    </button>
                @endif
                <button type="button" class="bm-btn secondary"
                    onclick="bmCloseModal('modal-edit-{{ $listing->id }}')">Cancel</button>
                <button type="submit" class="bm-btn primary">Save changes</button>
            </div>
        </form>
    </dialog>

    {{-- ── CALENDAR ── --}}
    <dialog id="modal-calendar-{{ $listing->id }}" class="bm-modal xl"
        data-events-url="{{ route('owner.listings.calendar.events', $listing) }}"
        data-listing-id="{{ $listing->id }}">
        <div class="bm-modal-head">
            <h2>{{ $listing->title }} — Calendar</h2>
            <button type="button" class="bm-modal-close"
                onclick="bmCloseModal('modal-calendar-{{ $listing->id }}')">&times;</button>
        </div>
        <div class="bm-modal-body">
            <div style="display:grid;grid-template-columns:1fr 260px;gap:20px;">

                <div>
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
                        <button type="button" class="bm-btn secondary sm bm-cal-prev"
                            data-lid="{{ $listing->id }}">&larr;</button>
                        <span class="bm-cal-title" data-lid="{{ $listing->id }}"
                            style="font-weight:600;color:var(--text-primary);"></span>
                        <button type="button" class="bm-btn secondary sm bm-cal-next"
                            data-lid="{{ $listing->id }}">&rarr;</button>
                    </div>
                    <div style="display:grid;grid-template-columns:repeat(7,1fr);gap:4px;text-align:center;font-size:11px;color:var(--text-secondary);margin-bottom:6px;">
                        <div>Sun</div><div>Mon</div><div>Tue</div><div>Wed</div>
                        <div>Thu</div><div>Fri</div><div>Sat</div>
                    </div>
                    <div class="bm-cal-grid" data-lid="{{ $listing->id }}"
                        style="display:grid;grid-template-columns:repeat(7,1fr);gap:4px;"></div>
                    <div style="display:flex;gap:16px;margin-top:12px;font-size:11px;color:var(--text-secondary);">
                        <span style="display:flex;align-items:center;gap:4px;">
                            <span style="width:8px;height:8px;border-radius:50%;background:#22c55e;display:inline-block;"></span>Confirmed
                        </span>
                        <span style="display:flex;align-items:center;gap:4px;">
                            <span style="width:8px;height:8px;border-radius:50%;background:#f59e0b;display:inline-block;"></span>Pending
                        </span>
                        <span style="display:flex;align-items:center;gap:4px;">
                            <span style="width:8px;height:8px;border-radius:50%;background:#71717a;display:inline-block;"></span>Blocked
                        </span>
                    </div>
                </div>

                <div style="display:flex;flex-direction:column;gap:18px;">
                    <div>
                        <div style="font-size:13px;font-weight:600;color:var(--text-secondary);margin-bottom:10px;">Block dates</div>
                        <form method="POST" action="{{ route('owner.listings.calendar.blocks.store', $listing) }}"
                            style="display:flex;flex-direction:column;gap:8px;">
                            @csrf
                            <select name="listing_unit_id" required class="bm-input">
                                <option value="">Select unit</option>
                                @foreach($listing->units as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                @endforeach
                            </select>
                            <input name="start_date" type="date" required min="{{ now()->toDateString() }}" class="bm-input">
                            <input name="end_date" type="date" required min="{{ now()->toDateString() }}" class="bm-input">
                            <input name="reason" type="text" placeholder="Reason (optional)" class="bm-input">
                            <button type="submit" class="bm-btn secondary">Block dates</button>
                        </form>
                    </div>
                    <div>
                        <div style="font-size:12px;color:var(--text-secondary);margin-bottom:6px;">Filter by unit</div>
                        <select class="bm-input bm-cal-unit-filter" data-lid="{{ $listing->id }}">
                            <option value="">All units</option>
                            @foreach($listing->units as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

            </div>
        </div>
    </dialog>

    {{-- ── ADD UNIT ── --}}
    <dialog id="modal-units-{{ $listing->id }}" class="bm-modal">
        <div class="bm-modal-head">
            <h2>Add {{ $listing->isRestaurant() ? 'table' : 'unit' }}</h2>
            <button type="button" class="bm-modal-close"
                onclick="bmOpenModal('modal-edit-{{ $listing->id }}')">&times;</button>
        </div>
        <form method="POST" action="{{ route('owner.listings.units.store', $listing) }}"
            style="display:flex;flex-direction:column;flex:1;min-height:0;">
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
                    <label>Price override
                        <span style="font-size:11px;color:var(--text-tertiary);">(optional)</span>
                    </label>
                    <input name="price_override" type="number" min="0" step="0.01"
                        placeholder="Leave blank to use base price">
                </div>
            </div>
            <div class="bm-modal-foot">
                <button type="button" class="bm-btn secondary"
                    onclick="bmOpenModal('modal-edit-{{ $listing->id }}')">Cancel</button>
                <button type="submit" class="bm-btn primary">Add</button>
            </div>
        </form>
    </dialog>

    {{-- Hidden helper forms --}}
    @foreach($listing->gallery() as $image)
        <form id="rm-img-{{ $image->id }}" method="POST"
            action="{{ route('owner.listings.images.destroy', [$listing, $image]) }}" style="display:none;">
            @csrf @method('DELETE')
        </form>
    @endforeach

    @foreach($listing->units as $unit)
        <form id="rm-unit-{{ $unit->id }}" method="POST"
            action="{{ route('owner.listings.units.destroy', [$listing, $unit]) }}" style="display:none;">
            @csrf @method('DELETE')
        </form>
    @endforeach

    @if($listing->status === 'draft')
        <form id="publish-{{ $listing->id }}" method="POST"
            action="{{ route('owner.listings.publish', $listing) }}" style="display:none;">
            @csrf
        </form>
    @endif

@endforeach

{{-- ─── CALENDAR JS ─────────────────────────────────────────────────────────── --}}
<script>
(() => {
    const state = {};

    function getState(lid) {
        if (!state[lid]) state[lid] = { date: new Date(), events: [] };
        return state[lid];
    }

    async function fetchEvents(lid) {
        const dialog = document.querySelector(`dialog[data-listing-id="${lid}"]`);
        if (!dialog) return;
        const url    = new URL(dialog.dataset.eventsUrl, location.origin);
        const unitId = document.querySelector(`.bm-cal-unit-filter[data-lid="${lid}"]`).value;
        if (unitId) url.searchParams.set('unit_id', unitId);
        try {
            const res = await fetch(url, { headers: { Accept: 'application/json' } });
            getState(lid).events = await res.json();
        } catch {
            getState(lid).events = [];
        }
        renderCalendar(lid);
    }

    function renderCalendar(lid) {
        const { date, events } = getState(lid);
        const year  = date.getFullYear();
        const month = date.getMonth();

        const titleEl = document.querySelector(`.bm-cal-title[data-lid="${lid}"]`);
        if (titleEl) titleEl.textContent =
            date.toLocaleString('default', { month: 'long', year: 'numeric' });

        const grid = document.querySelector(`.bm-cal-grid[data-lid="${lid}"]`);
        if (!grid) return;
        grid.innerHTML = '';

        const startOffset = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();

        for (let i = 0; i < startOffset; i++) grid.appendChild(document.createElement('div'));

        for (let day = 1; day <= daysInMonth; day++) {
            const ds = `${year}-${String(month + 1).padStart(2,'0')}-${String(day).padStart(2,'0')}`;
            const dayEvents = events.filter(e => ds >= e.start && ds < e.end);

            const cell = document.createElement('div');
            cell.style.cssText = 'aspect-ratio:1;border-radius:6px;border:1px solid var(--border-soft);padding:3px;font-size:11px;color:var(--text-secondary);overflow:hidden;';

            const lbl = document.createElement('div');
            lbl.textContent = day;
            cell.appendChild(lbl);

            dayEvents.slice(0, 2).forEach(ev => {
                const dot = document.createElement('div');
                dot.style.cssText = `background:${ev.color};border-radius:3px;padding:1px 3px;font-size:10px;color:white;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-top:1px;`;
                dot.textContent = ev.title;
                cell.appendChild(dot);
            });

            grid.appendChild(cell);
        }
    }

    document.querySelectorAll('.bm-cal-prev').forEach(btn => {
        btn.addEventListener('click', () => {
            const s = getState(btn.dataset.lid);
            s.date.setMonth(s.date.getMonth() - 1);
            renderCalendar(btn.dataset.lid);
        });
    });

    document.querySelectorAll('.bm-cal-next').forEach(btn => {
        btn.addEventListener('click', () => {
            const s = getState(btn.dataset.lid);
            s.date.setMonth(s.date.getMonth() + 1);
            renderCalendar(btn.dataset.lid);
        });
    });

    document.querySelectorAll('.bm-cal-unit-filter').forEach(sel => {
        sel.addEventListener('change', () => fetchEvents(sel.dataset.lid));
    });

    document.querySelectorAll('dialog[data-listing-id]').forEach(dialog => {
        dialog.addEventListener('toggle', () => {
            if (dialog.open) fetchEvents(dialog.dataset.listingId);
        });
    });
})();
</script>
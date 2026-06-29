<x-dashboard-layout title="Calendar - {{ $listing->title }}" active="listings" heading="Listing Calendar" :subheading="$listing->title">
    <div class="bm-inline-actions" style="justify-content:space-between;">
        <a href="{{ route('owner.listings.edit', $listing) }}" class="bm-btn secondary sm">Back to listing</a>
        <div class="bm-field" style="margin:0;min-width:220px;">
            <select id="unit-filter">
                <option value="">All units</option>
                @foreach($listing->units as $unit)
                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="bm-split-grid">
        <div class="bm-list-card">
            <div class="bm-list-head">
                <button id="prev-month" class="bm-btn secondary sm" type="button">&larr;</button>
                <h2 id="calendar-title"></h2>
                <button id="next-month" class="bm-btn secondary sm" type="button">&rarr;</button>
            </div>

            <div style="display:grid;grid-template-columns:repeat(7,1fr);gap:6px;text-align:center;font-size:12px;color:var(--text-tertiary);margin-bottom:8px;">
                <div>Sun</div><div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div>
            </div>
            <div id="calendar-grid" style="display:grid;grid-template-columns:repeat(7,1fr);gap:6px;"></div>

            <div class="bm-inline-actions" style="margin-top:16px;color:var(--text-secondary);font-size:12px;">
                <span class="bm-inline-actions" style="gap:6px;"><span style="width:9px;height:9px;border-radius:50%;background:var(--green);"></span> Confirmed</span>
                <span class="bm-inline-actions" style="gap:6px;"><span style="width:9px;height:9px;border-radius:50%;background:var(--amber);"></span> Pending</span>
                <span class="bm-inline-actions" style="gap:6px;"><span style="width:9px;height:9px;border-radius:50%;background:var(--text-tertiary);"></span> Blocked</span>
            </div>
        </div>

        <div>
            <div class="bm-form-card">
                <h2 style="font-size:16px;font-weight:700;margin:0 0 16px;">Block dates</h2>
                <form id="block-form" method="POST" action="{{ route('owner.listings.calendar.blocks.store', $listing) }}">
                    @csrf
                    <div class="bm-field">
                        <label for="listing_unit_id">Unit</label>
                        <select id="listing_unit_id" name="listing_unit_id" required>
                            <option value="">Select unit</option>
                            @foreach($listing->units as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="bm-field">
                        <label for="start_date">Start date</label>
                        <input id="start_date" name="start_date" type="date" required min="{{ now()->toDateString() }}">
                    </div>
                    <div class="bm-field">
                        <label for="end_date">End date</label>
                        <input id="end_date" name="end_date" type="date" required min="{{ now()->toDateString() }}">
                    </div>
                    <div class="bm-field">
                        <label for="reason">Reason <span class="hint">(optional)</span></label>
                        <input id="reason" name="reason" type="text">
                    </div>
                    <button type="submit" class="bm-btn secondary full">Block dates</button>
                </form>
            </div>

            <div id="event-list" style="margin-top:14px;"></div>
        </div>
    </div>

    <script>
        const eventsUrl = @json(route('owner.listings.calendar.events', $listing));
        let currentDate = new Date();
        let events = [];

        async function fetchEvents() {
            const unitId = document.getElementById('unit-filter').value;
            const url = new URL(eventsUrl);
            if (unitId) url.searchParams.set('unit_id', unitId);
            const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
            events = await res.json();
            renderCalendar();
        }

        function renderCalendar() {
            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();
            document.getElementById('calendar-title').textContent =
                currentDate.toLocaleString('default', { month: 'long', year: 'numeric' });

            const firstDay = new Date(year, month, 1);
            const startOffset = firstDay.getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();

            const grid = document.getElementById('calendar-grid');
            grid.innerHTML = '';

            for (let i = 0; i < startOffset; i++) {
                grid.appendChild(document.createElement('div'));
            }

            for (let day = 1; day <= daysInMonth; day++) {
                const cellDate = new Date(year, month, day);
                const cellDateStr = cellDate.toISOString().split('T')[0];
                const dayEvents = events.filter(e => cellDateStr >= e.start && cellDateStr < e.end);

                const cell = document.createElement('div');
                cell.style.aspectRatio = '1';
                cell.style.border = '1px solid var(--border)';
                cell.style.borderRadius = '10px';
                cell.style.padding = '6px';
                cell.style.fontSize = '12px';
                cell.style.color = 'var(--text-secondary)';
                cell.style.overflow = 'hidden';

                const dayLabel = document.createElement('div');
                dayLabel.textContent = day;
                dayLabel.style.color = 'var(--text-tertiary)';
                cell.appendChild(dayLabel);

                dayEvents.slice(0, 2).forEach(e => {
                    const dot = document.createElement('div');
                    dot.style.cssText = 'margin-top:4px;border-radius:6px;padding:2px 4px;font-size:10px;color:white;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;';
                    dot.style.backgroundColor = e.color;
                    dot.textContent = e.title;
                    cell.appendChild(dot);
                });

                grid.appendChild(cell);
            }
        }

        document.getElementById('prev-month').addEventListener('click', () => {
            currentDate.setMonth(currentDate.getMonth() - 1);
            renderCalendar();
        });
        document.getElementById('next-month').addEventListener('click', () => {
            currentDate.setMonth(currentDate.getMonth() + 1);
            renderCalendar();
        });
        document.getElementById('unit-filter').addEventListener('change', fetchEvents);

        fetchEvents();
    </script>
</x-dashboard-layout>

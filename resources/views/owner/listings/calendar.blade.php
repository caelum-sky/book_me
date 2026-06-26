<x-layout title="Calendar - {{ $listing->title }}">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <a href="{{ route('owner.listings.edit', $listing) }}" class="text-sm text-zinc-500 hover:text-zinc-300">&larr; Back to listing</a>

        <div class="flex items-center justify-between mt-3 mb-8">
            <h1 class="text-2xl font-bold text-white">{{ $listing->title }} &mdash; Calendar</h1>
            <select id="unit-filter" class="rounded-lg border border-zinc-700 bg-zinc-900 text-zinc-100 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">All units</option>
                @foreach($listing->units as $unit)
                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-zinc-900 border border-zinc-800 rounded-xl p-5">
                <div class="flex items-center justify-between mb-4">
                    <button id="prev-month" class="text-zinc-400 hover:text-white px-2 py-1">&larr;</button>
                    <h2 id="calendar-title" class="font-semibold text-white"></h2>
                    <button id="next-month" class="text-zinc-400 hover:text-white px-2 py-1">&rarr;</button>
                </div>

                <div class="grid grid-cols-7 gap-1 text-center text-xs text-zinc-500 mb-2">
                    <div>Sun</div><div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div>
                </div>
                <div id="calendar-grid" class="grid grid-cols-7 gap-1"></div>

                <div class="flex items-center gap-4 mt-4 text-xs text-zinc-500">
                    <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-green-500"></span> Confirmed</span>
                    <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> Pending</span>
                    <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-zinc-500"></span> Blocked</span>
                </div>
            </div>

            <div>
                <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-5">
                    <h2 class="font-semibold text-white mb-4">Block dates</h2>
                    <form id="block-form" method="POST" action="{{ route('owner.listings.calendar.blocks.store', $listing) }}" class="space-y-3">
                        @csrf
                        <select name="listing_unit_id" required
                            class="w-full rounded-lg border border-zinc-700 bg-zinc-950 text-zinc-100 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Select unit</option>
                            @foreach($listing->units as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                            @endforeach
                        </select>
                        <input name="start_date" type="date" required min="{{ now()->toDateString() }}"
                            class="w-full rounded-lg border border-zinc-700 bg-zinc-950 text-zinc-100 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <input name="end_date" type="date" required min="{{ now()->toDateString() }}"
                            class="w-full rounded-lg border border-zinc-700 bg-zinc-950 text-zinc-100 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <input name="reason" type="text" placeholder="Reason (optional)"
                            class="w-full rounded-lg border border-zinc-700 bg-zinc-950 text-zinc-100 px-3 py-2 text-sm placeholder-zinc-500 focus:border-blue-500 focus:ring-blue-500">
                        <button type="submit" class="w-full bg-zinc-800 text-zinc-200 rounded-lg py-2 text-sm font-medium hover:bg-zinc-700 transition">
                            Block dates
                        </button>
                    </form>
                </div>

                <div id="event-list" class="mt-4 space-y-2"></div>
            </div>
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
                cell.className = 'aspect-square rounded-lg border border-zinc-800 p-1 text-xs text-zinc-300 flex flex-col gap-0.5 overflow-hidden';

                const dayLabel = document.createElement('div');
                dayLabel.textContent = day;
                dayLabel.className = 'text-zinc-500';
                cell.appendChild(dayLabel);

                dayEvents.slice(0, 2).forEach(e => {
                    const dot = document.createElement('div');
                    dot.className = 'truncate rounded px-1 text-[10px] text-white';
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
</x-layout>
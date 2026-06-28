<x-public-layout title="My Calendar">

<style>
.cal-grid { display:grid; grid-template-columns:1fr 300px; gap:22px; align-items:start; }
@media(max-width:760px){ .cal-grid{grid-template-columns:1fr;} }

.cal-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:16px; }
.cal-nav-btn {
    width:34px;height:34px;border-radius:8px;border:1px solid var(--border);
    background:var(--panel-2);color:var(--text-secondary);cursor:pointer;
    display:flex;align-items:center;justify-content:center;font-family:inherit;
    transition:border-color .14s,color .14s;
}
.cal-nav-btn:hover { border-color:var(--border-hover);color:var(--text-primary); }

.cal-table { width:100%;border-collapse:collapse; }
.cal-table th { font-size:11.5px;font-weight:600;color:var(--text-tertiary);padding:0 0 10px;text-align:center;text-transform:uppercase;letter-spacing:.4px; }
.cal-day {
    text-align:center;padding:5px 2px;
    font-size:13px;cursor:default;border-radius:8px;
    position:relative;
}
.cal-day.other-month { color:var(--text-tertiary); }
.cal-day.today { color:var(--purple);font-weight:700; }
.cal-day.today::after {
    content:'';position:absolute;bottom:4px;left:50%;transform:translateX(-50%);
    width:4px;height:4px;border-radius:50%;background:var(--purple);
}
.cal-day.has-booking { cursor:pointer; }
.cal-day.has-booking .day-num {
    display:inline-flex;align-items:center;justify-content:center;
    width:28px;height:28px;border-radius:50%;
    background:rgba(167,139,250,.18);color:var(--purple);font-weight:600;
}
.cal-day.has-booking:hover .day-num { background:rgba(167,139,250,.3); }
.cal-day.confirmed .day-num { background:rgba(52,211,153,.15);color:var(--green); }
.cal-day.pending .day-num   { background:rgba(251,191,36,.12);color:var(--amber); }

.cal-dot { position:absolute;bottom:3px;left:50%;transform:translateX(-50%);display:flex;gap:2px; }
.dot { width:4px;height:4px;border-radius:50%; }

.event-item {
    display:flex;align-items:center;gap:10px;padding:10px 12px;
    border-radius:var(--radius);border:1px solid var(--border);
    margin-bottom:8px;text-decoration:none;color:inherit;
    transition:border-color .14s,background .14s;
}
.event-item:hover { border-color:var(--border-hover);background:var(--panel-3); }
.event-dot { width:8px;height:8px;border-radius:50%;flex-shrink:0; }
.event-info .name { font-size:13px;font-weight:500; }
.event-info .date { font-size:12px;color:var(--text-tertiary);margin-top:2px; }
</style>

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:22px;flex-wrap:wrap;gap:12px;">
    <div>
        <h1 style="font-size:22px;font-weight:700;">My Calendar</h1>
        <p style="color:var(--text-secondary);font-size:13px;margin-top:4px;">Your bookings at a glance.</p>
    </div>
    <a href="{{ route('listings.index') }}" class="bm-btn primary sm">+ New booking</a>
</div>

<div class="cal-grid">
    {{-- Calendar --}}
    <div>
        <div class="bm-card" style="padding:22px;">
            <div class="cal-header">
                <button class="cal-nav-btn" id="prev-month" onclick="changeMonth(-1)">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                </button>
                <h2 id="cal-title" style="font-size:15px;font-weight:700;"></h2>
                <button class="cal-nav-btn" id="next-month" onclick="changeMonth(1)">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                </button>
            </div>

            <table class="cal-table">
                <thead>
                    <tr>
                        @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $d)
                            <th>{{ $d }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody id="cal-body"></tbody>
            </table>

            {{-- Legend --}}
            <div style="display:flex;align-items:center;gap:14px;margin-top:16px;padding-top:14px;border-top:1px solid var(--border);flex-wrap:wrap;">
                <div style="display:flex;align-items:center;gap:5px;font-size:12px;color:var(--text-secondary);">
                    <span class="dot" style="background:var(--green);width:8px;height:8px;"></span>Confirmed
                </div>
                <div style="display:flex;align-items:center;gap:5px;font-size:12px;color:var(--text-secondary);">
                    <span class="dot" style="background:var(--amber);width:8px;height:8px;"></span>Pending
                </div>
                <div style="display:flex;align-items:center;gap:5px;font-size:12px;color:var(--text-secondary);">
                    <span class="dot" style="background:var(--purple);width:8px;height:8px;"></span>Today
                </div>
            </div>
        </div>
    </div>

    {{-- Event list sidebar --}}
    <div>
        <div class="bm-form-card" style="margin-bottom:14px;">
            <h3 id="sidebar-title" style="font-size:14px;font-weight:700;margin-bottom:14px;">This month</h3>
            <div id="event-list">
                @forelse($upcomingBookings as $booking)
                    <a href="{{ route('bookings.show', $booking) }}" class="event-item">
                        <div class="event-dot" style="background:{{ $booking->status === 'confirmed' ? 'var(--green)' : 'var(--amber)' }};"></div>
                        <div class="event-info">
                            <div class="name">{{ Str::limit($booking->listing->title, 30) }}</div>
                            <div class="date">{{ $booking->check_in->format('M j') }}{{ $booking->check_out ? ' – '.$booking->check_out->format('M j') : '' }}</div>
                        </div>
                    </a>
                @empty
                    <div style="font-size:13px;color:var(--text-tertiary);text-align:center;padding:20px 0;">No upcoming bookings.</div>
                @endforelse
            </div>
        </div>

        <div class="bm-form-card">
            <h3 style="font-size:14px;font-weight:700;margin-bottom:12px;">Jump to</h3>
            <a href="{{ route('bookings.index', ['status' => 'confirmed']) }}" class="bm-btn secondary full sm" style="margin-bottom:8px;display:flex;">Confirmed bookings</a>
            <a href="{{ route('bookings.index', ['status' => 'pending']) }}" class="bm-btn secondary full sm" style="margin-bottom:8px;display:flex;">Pending bookings</a>
            <a href="{{ route('bookings.history') }}" class="bm-btn secondary full sm" style="display:flex;">Full history</a>
        </div>
    </div>
</div>

<script>
const bookings = @json($calendarBookings ?? []);
let viewYear, viewMonth;

const today = new Date();
viewYear  = today.getFullYear();
viewMonth = today.getMonth();

function changeMonth(dir) {
    viewMonth += dir;
    if (viewMonth > 11) { viewMonth = 0; viewYear++; }
    if (viewMonth < 0)  { viewMonth = 11; viewYear--; }
    renderCalendar();
}

function renderCalendar() {
    const monthNames = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    document.getElementById('cal-title').textContent = monthNames[viewMonth] + ' ' + viewYear;
    document.getElementById('sidebar-title').textContent = monthNames[viewMonth] + ' ' + viewYear;

    const firstDay = new Date(viewYear, viewMonth, 1).getDay();
    const daysInMonth = new Date(viewYear, viewMonth + 1, 0).getDate();

    // Build a map of booking dates
    const dateMap = {};
    bookings.forEach(b => {
        const cin  = new Date(b.check_in);
        const cout = b.check_out ? new Date(b.check_out) : cin;
        const cur  = new Date(cin);
        while (cur <= cout) {
            if (cur.getFullYear() === viewYear && cur.getMonth() === viewMonth) {
                const d = cur.getDate();
                if (!dateMap[d]) dateMap[d] = [];
                dateMap[d].push(b);
            }
            cur.setDate(cur.getDate() + 1);
        }
    });

    let html = '<tr>';
    // Leading empty cells
    for (let i = 0; i < firstDay; i++) html += '<td class="cal-day other-month"></td>';
    let col = firstDay;

    for (let d = 1; d <= daysInMonth; d++) {
        const isToday = (d === today.getDate() && viewMonth === today.getMonth() && viewYear === today.getFullYear());
        const dayBookings = dateMap[d] || [];
        let cls = 'cal-day';
        if (isToday) cls += ' today';
        if (dayBookings.length > 0) {
            cls += ' has-booking';
            const statuses = dayBookings.map(b => b.status);
            if (statuses.includes('confirmed')) cls += ' confirmed';
            else if (statuses.includes('pending')) cls += ' pending';
        }

        let dots = '';
        dayBookings.slice(0, 3).forEach(b => {
            const col = b.status === 'confirmed' ? 'var(--green)' : 'var(--amber)';
            dots += `<span class="dot" style="background:${col};"></span>`;
        });

        let link = dayBookings.length === 1
            ? `onclick="window.location='/bookings/${dayBookings[0].id}'"` : '';

        html += `<td class="${cls}" ${link}><span class="day-num">${d}</span>${dots ? '<div class="cal-dot">'+dots+'</div>' : ''}</td>`;

        col++;
        if (col % 7 === 0 && d < daysInMonth) { html += '</tr><tr>'; }
    }
    // Trailing cells
    const remaining = 7 - (col % 7);
    if (remaining < 7) for (let i = 0; i < remaining; i++) html += '<td class="cal-day other-month"></td>';
    html += '</tr>';

    document.getElementById('cal-body').innerHTML = html;

    // Update sidebar event list for this month
    const monthBookings = bookings.filter(b => {
        const d = new Date(b.check_in);
        return d.getFullYear() === viewYear && d.getMonth() === viewMonth;
    });

    const evList = document.getElementById('event-list');
    if (monthBookings.length === 0) {
        evList.innerHTML = '<div style="font-size:13px;color:var(--text-tertiary);text-align:center;padding:20px 0;">No bookings this month.</div>';
    } else {
        evList.innerHTML = monthBookings.map(b => {
            const color = b.status === 'confirmed' ? 'var(--green)' : 'var(--amber)';
            const dateStr = b.check_in ? new Date(b.check_in).toLocaleDateString('en-US',{month:'short',day:'numeric'}) : '';
            return `<a href="/bookings/${b.id}" class="event-item">
                <div class="event-dot" style="background:${color};"></div>
                <div class="event-info">
                    <div class="name">${b.listing_title || 'Booking'}</div>
                    <div class="date">${dateStr}</div>
                </div>
            </a>`;
        }).join('');
    }
}

renderCalendar();
</script>

</x-public-layout>

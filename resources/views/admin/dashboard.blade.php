<x-dashboard-layout title="Admin Dashboard" active="dashboard" subheading="Platform-wide oversight and approvals">

    <div class="bm-stat-row cols-4">
        <div class="bm-stat-card">
            <div class="bm-stat-top">
                <div class="bm-stat-icon purple">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 3.6-7 8-7s8 3 8 7"/></svg>
                </div>
                <div class="bm-stat-title">Total users</div>
            </div>
            <div class="bm-stat-value">{{ $stats['total_users'] }}</div>
            <div class="bm-stat-delta up">
                <span class="muted">{{ $stats['total_customers'] }} customers &middot; {{ $stats['total_owners'] }} owners</span>
            </div>
        </div>

        <div class="bm-stat-card">
            <div class="bm-stat-top">
                <div class="bm-stat-icon blue">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="3"/><path d="M3 9h18M8 2v4M16 2v4"/></svg>
                </div>
                <div class="bm-stat-title">Total bookings</div>
            </div>
            <div class="bm-stat-value">{{ $stats['total_bookings'] }}</div>
            <div class="bm-stat-delta up">
                <span class="muted">{{ $stats['bookings_this_month'] }} this month</span>
            </div>
        </div>

        <div class="bm-stat-card" style="{{ $stats['pending_businesses'] > 0 ? 'box-shadow: inset 0 0 0 1px rgba(255,176,59,0.3);' : '' }}">
            <div class="bm-stat-top">
                <div class="bm-stat-icon amber">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="bm-stat-title">Pending businesses</div>
            </div>
            <div class="bm-stat-value" style="{{ $stats['pending_businesses'] > 0 ? 'color:#ffc169;' : '' }}">{{ $stats['pending_businesses'] }}</div>
            <div class="bm-stat-delta">
                <span class="muted">awaiting review</span>
            </div>
        </div>

        <div class="bm-stat-card" style="{{ $stats['pending_listings'] > 0 ? 'box-shadow: inset 0 0 0 1px rgba(255,176,59,0.3);' : '' }}">
            <div class="bm-stat-top">
                <div class="bm-stat-icon amber">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9.5 12 3l9 6.5V20a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1Z"/></svg>
                </div>
                <div class="bm-stat-title">Pending listings</div>
            </div>
            <div class="bm-stat-value" style="{{ $stats['pending_listings'] > 0 ? 'color:#ffc169;' : '' }}">{{ $stats['pending_listings'] }}</div>
            <div class="bm-stat-delta">
                <span class="muted">awaiting review</span>
            </div>
        </div>
    </div>

    @if($stats['pending_businesses'] > 0 || $stats['pending_listings'] > 0)
        <div class="bm-list-card" style="border-color: rgba(255,176,59,0.25); background: rgba(255,176,59,0.05);">
            <div class="bm-list-head">
                <h2 style="display:flex;align-items:center;gap:8px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ffc169" stroke-width="2"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Needs your attention
                </h2>
            </div>

            @if($stats['pending_businesses'] > 0)
                <a href="{{ route('admin.businesses.index', ['status' => 'pending']) }}" class="bm-list-row">
                    <div class="bm-row-avatar" style="background:linear-gradient(135deg,#ffb03b,#ec4faa);">{{ $stats['pending_businesses'] }}</div>
                    <div class="bm-row-info">
                        <div class="name">{{ $stats['pending_businesses'] }} business{{ $stats['pending_businesses'] === 1 ? '' : 'es' }} waiting for approval</div>
                        <div class="meta">New vendor signups need a review before they can list anything</div>
                    </div>
                    <div class="bm-row-badge badge-amber">Review &rarr;</div>
                </a>
            @endif

            @if($stats['pending_listings'] > 0)
                <a href="{{ route('admin.listings.index', ['status' => 'pending_review']) }}" class="bm-list-row">
                    <div class="bm-row-avatar" style="background:linear-gradient(135deg,#7c5cff,#4f9bff);">{{ $stats['pending_listings'] }}</div>
                    <div class="bm-row-info">
                        <div class="name">{{ $stats['pending_listings'] }} listing{{ $stats['pending_listings'] === 1 ? '' : 's' }} pending review</div>
                        <div class="meta">Submitted by approved vendors, ready to go live once approved</div>
                    </div>
                    <div class="bm-row-badge badge-amber">Review &rarr;</div>
                </a>
            @endif
        </div>
    @else
        <div class="bm-list-card">
            <div class="bm-empty">
                <div class="icon">&#10003;</div>
                <div>All caught up — nothing pending review right now.</div>
            </div>
        </div>
    @endif

    <div class="bm-list-card">
        <div class="bm-list-head">
            <h2>Recent activity</h2>
        </div>

        @forelse($recentActivity as $log)
            <div class="bm-list-row" style="cursor:default;">
                <div class="bm-row-avatar" style="background:linear-gradient(135deg,#4f9bff,#7c5cff);font-size:12px;">
                    {{ $log->user ? collect(explode(' ', $log->user->name))->map(fn($p) => mb_substr($p,0,1))->take(2)->implode('') : '⚙' }}
                </div>
                <div class="bm-row-info">
                    <div class="name">{{ $log->user->name ?? 'System' }}</div>
                    <div class="meta">{{ str_replace('.', ' ', $log->action) }}</div>
                </div>
                <div class="bm-row-link" style="width:auto;">{{ $log->created_at->diffForHumans() }}</div>
            </div>
        @empty
            <div class="bm-empty">
                <div>No activity yet.</div>
            </div>
        @endforelse
    </div>

</x-dashboard-layout>
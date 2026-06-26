<x-dashboard-layout title="Business Approvals" active="businesses" subheading="Review and manage vendor accounts">

    <div class="bm-tab-row">
        @foreach(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', 'suspended' => 'Suspended', 'all' => 'All'] as $value => $label)
            <a href="{{ route('admin.businesses.index', ['status' => $value]) }}" class="bm-tab {{ $status === $value ? 'active' : '' }}">
                {{ $label }}
            </a>
        @endforeach
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

                    <div style="margin-top:10px;">
                        <a href="{{ route('admin.businesses.show', $business) }}" style="font-size:12.5px;color:#a08bff;text-decoration:none;font-weight:600;">View details &rarr;</a>
                    </div>

                    @if($business->status === 'pending')
                        <div style="display:flex;gap:8px;margin-top:12px;">
                            <form method="POST" action="{{ route('admin.businesses.approve', $business) }}">
                                @csrf
                                <button type="submit" class="bm-btn success sm">Approve</button>
                            </form>
                            <button type="button" onclick="document.getElementById('reject-business-{{ $business->id }}').classList.toggle('hidden')" class="bm-btn danger sm">Reject</button>
                        </div>
                        <form id="reject-business-{{ $business->id }}" method="POST" action="{{ route('admin.businesses.reject', $business) }}" class="hidden" style="margin-top:10px;display:flex;gap:8px;max-width:420px;">
                            @csrf
                            <input name="rejection_reason" type="text" placeholder="Reason for rejection" required
                                style="flex:1;background:var(--panel);border:1px solid var(--border-soft);border-radius:10px;padding:9px 12px;color:var(--text-primary);font-size:13px;">
                            <button type="submit" class="bm-btn danger sm">Confirm</button>
                        </form>
                    @elseif($business->status === 'approved')
                        <form method="POST" action="{{ route('admin.businesses.suspend', $business) }}" style="margin-top:12px;"
                            onsubmit="return confirm('Suspend this business?');">
                            @csrf
                            <button type="submit" class="bm-btn secondary sm">Suspend</button>
                        </form>
                    @endif
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

    <div>{{ $businesses->links() }}</div>

</x-dashboard-layout>
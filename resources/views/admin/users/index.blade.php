<x-dashboard-layout title="User Management" active="users" subheading="Manage customer and business owner accounts">

    <x-slot:search>
        <form method="GET" action="{{ route('admin.users.index') }}" style="display:flex;gap:10px;width:100%;align-items:center;">
            <input type="text" name="search" placeholder="Search name or email..." value="{{ request('search') }}"
                style="background:transparent;border:none;outline:none;color:var(--text-primary);font-size:13.5px;flex:1;">
        </form>
    </x-slot:search>

    <div class="bm-field-row" style="max-width:480px;">
        <form method="GET" action="{{ route('admin.users.index') }}" style="display:flex;gap:10px;">
            <select name="role" onchange="this.form.submit()"
                style="background:var(--panel-2);border:1px solid var(--border-soft);border-radius:10px;padding:9px 14px;color:var(--text-primary);font-size:13px;">
                <option value="">All roles</option>
                <option value="customer" {{ request('role') === 'customer' ? 'selected' : '' }}>Customers</option>
                <option value="business_owner" {{ request('role') === 'business_owner' ? 'selected' : '' }}>Business owners</option>
            </select>
            @if(request('search'))
                <input type="hidden" name="search" value="{{ request('search') }}">
            @endif
        </form>
    </div>

    <div class="bm-list-card">
        @forelse($users as $user)
            <div class="bm-list-row" style="cursor:default;">
                <div class="bm-row-avatar" style="background:linear-gradient(135deg,#ec4faa,#7c5cff);">
                    {{ collect(explode(' ', $user->name))->map(fn($p) => mb_substr($p,0,1))->take(2)->implode('') }}
                </div>
                <div class="bm-row-info">
                    <div class="name">{{ $user->name }}</div>
                    <div class="meta">{{ $user->email }} &middot; {{ ucfirst(str_replace('_', ' ', $user->role)) }}</div>
                </div>

                @if($user->isBusinessOwner())
                    @if(!$user->business)
                        <div class="bm-row-badge badge-red" title="This owner never created a business profile">No business yet</div>
                    @endif
                    <div class="bm-row-badge
                        @if($user->approval_status === 'approved') badge-green
                        @elseif($user->approval_status === 'pending') badge-amber
                        @else badge-red
                        @endif">
                        {{ ucfirst($user->approval_status) }}
                    </div>
                @endif

                <div class="bm-row-badge {{ $user->is_active ? 'badge-gray' : 'badge-red' }}">
                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                </div>

                <form method="POST" action="{{ route('admin.users.toggle-active', $user) }}">
                    @csrf
                    <button type="submit" class="bm-btn secondary sm">
                        {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                    </button>
                </form>

                <a href="{{ route('admin.users.edit', $user) }}" class="bm-btn primary sm">Edit</a>
            </div>
        @empty
            <div class="bm-empty">No users found.</div>
        @endforelse
    </div>

    <div>{{ $users->links() }}</div>

</x-dashboard-layout>

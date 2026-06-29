<x-dashboard-layout title="Pending Approval" active="dashboard" subheading="Your owner account is not fully active yet.">
    <div class="bm-list-card" style="max-width:680px;">
        <div class="bm-empty">
            <div class="icon">
                <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 8v4l3 3"/><circle cx="12" cy="12" r="9"/></svg>
            </div>

            @if(!$user->business)
                <h2 style="font-size:20px;font-weight:800;margin:0 0 8px;color:var(--text-primary);">Set up your business</h2>
                <p style="margin:0 auto 22px;max-width:460px;line-height:1.6;">
                    Before you can list rooms, units, or tables, tell us about your business.
                    A super-admin will review it before you get full access.
                </p>
                <a href="{{ route('owner.business.create') }}" class="bm-btn primary">Set up business profile</a>
            @elseif($user->isPendingApproval())
                <h2 style="font-size:20px;font-weight:800;margin:0 0 8px;color:var(--text-primary);">Awaiting approval</h2>
                <p style="margin:0 auto;max-width:480px;line-height:1.6;">
                    Your business profile <strong style="color:var(--text-primary);">{{ $user->business->name }}</strong>
                    is under review. You will get full access to manage listings, bookings, and calendars once approved.
                </p>
            @elseif($user->approval_status === 'rejected')
                <h2 style="font-size:20px;font-weight:800;margin:0 0 8px;color:var(--text-primary);">Application rejected</h2>
                <p style="margin:0 auto 16px;max-width:440px;line-height:1.6;">Your business application was not approved.</p>
                @if($user->rejection_reason)
                    <div class="bm-alert error" style="text-align:left;margin-bottom:18px;">
                        <span>{{ $user->rejection_reason }}</span>
                    </div>
                @endif
                <p style="font-size:12.5px;color:var(--text-tertiary);margin:0;">Contact support if you believe this was a mistake.</p>
            @elseif($user->approval_status === 'suspended')
                <h2 style="font-size:20px;font-weight:800;margin:0 0 8px;color:var(--text-primary);">Account suspended</h2>
                <p style="margin:0 auto;max-width:420px;line-height:1.6;">Your business account has been suspended. Contact support for details.</p>
            @endif
        </div>
    </div>
</x-dashboard-layout>

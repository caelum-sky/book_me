<x-layout title="Pending Approval">
    <div class="max-w-md mx-auto px-4 py-16 text-center">
        <div class="w-16 h-16 bg-amber-950/50 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-8 h-8 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>

        @if(!$user->business)
            <h1 class="text-2xl font-bold mb-2 text-white">Set up your business</h1>
            <p class="text-zinc-400 text-sm mb-8">
                Before you can list rooms, units, or tables, tell us about your business.
                A super-admin will review it before you get full access.
            </p>
            <a href="{{ route('owner.business.create') }}" class="block w-full bg-blue-600 text-white rounded-lg py-2.5 text-sm font-medium hover:bg-blue-500 transition">
                Set up business profile
            </a>
        @elseif($user->isPendingApproval())
            <h1 class="text-2xl font-bold mb-2 text-white">Awaiting approval</h1>
            <p class="text-zinc-400 text-sm mb-8">
                Your business profile <strong class="text-zinc-200">{{ $user->business->name }}</strong> has been submitted
                and is currently under review by our team. You'll get full access to manage listings,
                bookings, and your calendar once approved.
            </p>
        @elseif($user->approval_status === 'rejected')
            <h1 class="text-2xl font-bold mb-2 text-white">Application rejected</h1>
            <p class="text-zinc-400 text-sm mb-4">
                Unfortunately your business application wasn't approved.
            </p>
            @if($user->rejection_reason)
                <div class="rounded-lg bg-red-950/50 border border-red-900 text-red-400 px-4 py-3 text-sm mb-6 text-left">
                    {{ $user->rejection_reason }}
                </div>
            @endif
            <p class="text-zinc-500 text-xs">Contact support if you believe this was a mistake.</p>
        @elseif($user->approval_status === 'suspended')
            <h1 class="text-2xl font-bold mb-2 text-white">Account suspended</h1>
            <p class="text-zinc-400 text-sm mb-8">Your business account has been suspended. Contact support for details.</p>
        @endif

        <form method="POST" action="{{ route('logout') }}" class="mt-3">
            @csrf
            <button type="submit" class="w-full border border-zinc-700 text-zinc-200 rounded-lg py-2.5 text-sm font-medium hover:bg-zinc-800 transition">
                Log out
            </button>
        </form>
    </div>
</x-layout>
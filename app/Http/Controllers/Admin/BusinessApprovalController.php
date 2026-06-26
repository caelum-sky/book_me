<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Business;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BusinessApprovalController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->input('status', 'pending');

        $businesses = Business::with('owner')
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.businesses.index', compact('businesses', 'status'));
    }

    public function show(Business $business): View
    {
        $business->load('owner', 'listings');

        return view('admin.businesses.show', compact('business'));
    }

    public function approve(Business $business): RedirectResponse
    {
        $this->authorize('approve', $business);

        $business->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        // Approving the business also flips the owning user's approval status,
        // which is the actual gate checked by EnsureBusinessOwnerApproved middleware.
        $business->owner->update([
            'approval_status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        ActivityLog::record('business.approved', $business);

        return back()->with('status', 'Business approved. Owner now has full access.');
    }

    public function reject(Request $request, Business $business): RedirectResponse
    {
        $this->authorize('approve', $business);

        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
        ]);

        $business->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        $business->owner->update([
            'approval_status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        ActivityLog::record('business.rejected', $business, $validated);

        return back()->with('status', 'Business rejected.');
    }

    public function suspend(Business $business): RedirectResponse
    {
        $this->authorize('approve', $business);

        $business->update(['status' => 'suspended', 'is_active' => false]);
        $business->owner->update(['approval_status' => 'suspended']);

        ActivityLog::record('business.suspended', $business);

        return back()->with('status', 'Business suspended.');
    }
}

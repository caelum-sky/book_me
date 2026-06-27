<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Business;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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

        // Needed for the "New business" modal's owner picker - only
        // business owners without an existing business can be assigned.
        $availableOwners = User::where('role', 'business_owner')
            ->doesntHave('business')
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return view('admin.businesses.index', compact('businesses', 'status', 'availableOwners'));
    }

    public function show(Business $business): View
    {
        $business->load('owner', 'listings');

        return view('admin.businesses.show', compact('business'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Business::class);

        $validated = $request->validate([
            'owner_id' => ['required', 'exists:users,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'in:pending,approved,rejected,suspended'],
        ]);

        $owner = User::findOrFail($validated['owner_id']);
        abort_if($owner->business()->exists(), 422, 'This user already has a business.');

        $business = Business::create([
            ...collect($validated)->except('owner_id', 'status')->all(),
            'owner_id' => $owner->id,
            'slug' => Str::slug($validated['name']).'-'.Str::random(6),
            'status' => $validated['status'],
            'approved_by' => $validated['status'] === 'approved' ? auth()->id() : null,
            'approved_at' => $validated['status'] === 'approved' ? now() : null,
        ]);

        // Keep the owner's own approval_status in sync with the business,
        // same rule the normal approve/reject/suspend actions follow.
        $owner->update(['approval_status' => $validated['status'] === 'approved' ? 'approved' : $owner->approval_status]);

        ActivityLog::record('business.created_by_admin', $business);

        return to_route('admin.businesses.index')->with('status', 'Business created.');
    }

    public function update(Request $request, Business $business): RedirectResponse
    {
        $this->authorize('update', $business);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'in:pending,approved,rejected,suspended'],
        ]);

        $wasApproved = $business->isApproved();

        $business->update($validated);

        if ($validated['status'] === 'approved' && ! $wasApproved) {
            $business->update(['approved_by' => auth()->id(), 'approved_at' => now()]);
            $business->owner->update(['approval_status' => 'approved', 'approved_by' => auth()->id(), 'approved_at' => now()]);
        } elseif ($validated['status'] !== 'approved') {
            $business->owner->update(['approval_status' => $validated['status']]);
        }

        ActivityLog::record('business.updated_by_admin', $business);

        return back()->with('status', 'Business updated.');
    }

    public function destroy(Business $business): RedirectResponse
    {
        $this->authorize('delete', $business);

        ActivityLog::record('business.deleted_by_admin', null, ['name' => $business->name]);

        $business->delete();

        return to_route('admin.businesses.index')->with('status', 'Business deleted.');
    }

    public function approve(Business $business): RedirectResponse
    {
        $this->authorize('approve', $business);

        $business->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

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
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Listing;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ListingApprovalController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->input('status', 'pending_review');

        $listings = Listing::with('business.owner')
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.listings.index', compact('listings', 'status'));
    }

    public function show(Listing $listing): View
    {
        $listing->load('business.owner', 'allMedia', 'units');

        return view('admin.listings.show', compact('listing'));
    }

    public function approve(Listing $listing): RedirectResponse
    {
        $this->authorize('approve', $listing);

        $listing->update(['status' => 'published']);
        ActivityLog::record('listing.approved', $listing);

        return back()->with('status', 'Listing approved and published.');
    }

    public function reject(Request $request, Listing $listing): RedirectResponse
    {
        $this->authorize('approve', $listing);

        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
        ]);

        $listing->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        ActivityLog::record('listing.rejected', $listing, $validated);

        return back()->with('status', 'Listing rejected.');
    }

    public function suspend(Listing $listing): RedirectResponse
    {
        $this->authorize('approve', $listing);

        $listing->update(['status' => 'suspended', 'is_active' => false]);
        ActivityLog::record('listing.suspended', $listing);

        return back()->with('status', 'Listing suspended.');
    }
}
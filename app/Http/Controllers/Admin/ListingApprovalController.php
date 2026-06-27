<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Business;
use App\Models\Listing;
use App\Models\ListingUnit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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

        // Needed for the "New listing" modal's business picker.
        $businesses = Business::approved()->orderBy('name')->get(['id', 'name']);

        return view('admin.listings.index', compact('listings', 'status', 'businesses'));
    }

    public function show(Listing $listing): View
    {
        $listing->load('business.owner', 'allMedia', 'units');

        return view('admin.listings.show', compact('listing'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Listing::class);

        $validated = $this->validateListing($request);

        $business = Business::findOrFail($validated['business_id']);

        $listing = $business->listings()->create([
            ...collect($validated)->except('business_id', 'status')->all(),
            'slug' => Str::slug($validated['title']).'-'.Str::random(6),
            'status' => $validated['status'],
        ]);

        $this->handleImageUploads($request, $listing);

        ActivityLog::record('listing.created_by_admin', $listing);

        return to_route('admin.listings.index')->with('status', 'Listing created.');
    }

    public function update(Request $request, Listing $listing): RedirectResponse
    {
        $this->authorize('update', $listing);

        $validated = $this->validateListing($request, includeBusiness: false);

        $listing->update($validated);

        $this->handleImageUploads($request, $listing);

        ActivityLog::record('listing.updated_by_admin', $listing);

        return back()->with('status', 'Listing updated.');
    }

    public function destroy(Listing $listing): RedirectResponse
    {
        $this->authorize('delete', $listing);

        ActivityLog::record('listing.deleted_by_admin', null, ['title' => $listing->title]);

        $listing->delete();

        return to_route('admin.listings.index')->with('status', 'Listing deleted.');
    }

    public function storeUnit(Request $request, Listing $listing): RedirectResponse
    {
        $this->authorize('update', $listing);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'capacity' => ['required', 'integer', 'min:1'],
            'quantity' => ['required', 'integer', 'min:1'],
            'price_override' => ['nullable', 'numeric', 'min:0'],
        ]);

        $listing->units()->create($validated);

        return back()->with('status', 'Unit added.');
    }

    public function destroyUnit(Listing $listing, ListingUnit $unit): RedirectResponse
    {
        $this->authorize('update', $listing);
        abort_unless($unit->listing_id === $listing->id, 404);

        $unit->delete();

        return back()->with('status', 'Unit removed.');
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

    protected function validateListing(Request $request, bool $includeBusiness = true): array
    {
        $isRestaurant = $request->input('type') === 'restaurant';

        $rules = [
            'type' => ['required', 'in:'.implode(',', Listing::TYPES)],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'max_guests' => [$isRestaurant ? 'nullable' : 'required', 'integer', 'min:1'],
            'bedrooms' => ['nullable', 'integer', 'min:0'],
            'bathrooms' => ['nullable', 'integer', 'min:0'],
            'seating_capacity' => [$isRestaurant ? 'required' : 'nullable', 'integer', 'min:1'],
            'cuisine_type' => ['nullable', 'string', 'max:100'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'price_unit' => ['required', 'in:per_night,per_reservation,per_person'],
            'status' => ['required', 'in:draft,pending_review,published,rejected,suspended'],
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['image', 'max:8192'],
        ];

        if ($includeBusiness) {
            $rules['business_id'] = ['required', 'exists:businesses,id'];
        }

        return $request->validate($rules);
    }

    protected function handleImageUploads(Request $request, Listing $listing): void
    {
        if (! $request->hasFile('images')) {
            return;
        }

        foreach ($request->file('images') as $file) {
            $listing->addGalleryMedia($file);
        }
    }
}
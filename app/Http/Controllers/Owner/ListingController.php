<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Listing;
use App\Models\Media;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ListingController extends Controller
{
    public function index(Request $request): View
    {
        $listings = $request->user()->business->listings()
            ->withCount('bookings')
            ->with('allMedia', 'units')
            ->latest()
            ->paginate(10);

        return view('owner.listings.index', [
            'listings' => $listings,
            'types' => Listing::TYPES,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Listing::class);

        return view('owner.listings.create', ['types' => Listing::TYPES]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Listing::class);

        $validated = $this->validateListing($request);

        $listing = $request->user()->business->listings()->create([
            ...$validated,
            'slug' => Str::slug($validated['title']).'-'.Str::random(6),
            'status' => 'pending_review',
        ]);

        $this->handleImageUploads($request, $listing);

        ActivityLog::record('listing.created', $listing);

        return to_route('owner.listings.index')
            ->with('status', 'Listing created and submitted for review.');
    }

    public function edit(Listing $listing): View
    {
        $this->authorize('update', $listing);

        $listing->load('allMedia', 'units');

        return view('owner.listings.edit', ['listing' => $listing, 'types' => Listing::TYPES]);
    }

    public function update(Request $request, Listing $listing): RedirectResponse
    {
        $this->authorize('update', $listing);

        $validated = $this->validateListing($request);

        $listing->update($validated);

        $this->handleImageUploads($request, $listing);

        ActivityLog::record('listing.updated', $listing);

        return back()->with('status', 'Listing updated.');
    }

    public function destroy(Listing $listing): RedirectResponse
    {
        $this->authorize('delete', $listing);

        ActivityLog::record('listing.deleted', $listing, ['title' => $listing->title]);

        $listing->delete();

        return to_route('owner.listings.index')->with('status', 'Listing removed.');
    }

    public function publish(Listing $listing): RedirectResponse
    {
        $this->authorize('update', $listing);

        if ($listing->status === 'draft') {
            $listing->update(['status' => 'pending_review']);
            ActivityLog::record('listing.submitted_for_review', $listing);

            return back()->with('status', 'Listing submitted for super-admin review.');
        }

        return back()->withErrors(['status' => 'Only draft listings can be submitted for review.']);
    }

    /**
     * Remove a single gallery image. Route still receives a Media id
     * (renamed from the old "image" param at the route level is NOT
     * required - Laravel resolves {image} to a Media model fine since
     * route model binding works off the parameter name, not the class).
     */
    public function destroyImage(Listing $listing, Media $image): RedirectResponse
    {
        $this->authorize('update', $listing);
        abort_unless(
            $image->mediable_type === Listing::class && $image->mediable_id === $listing->id,
            404
        );

        $image->deleteWithFile();

        return back()->with('status', 'Image removed.');
    }

    protected function validateListing(Request $request): array
    {
        $isRestaurant = $request->input('type') === 'restaurant';

        return $request->validate([
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
            'amenities' => ['nullable', 'array'],
            'amenities.*' => ['string', 'max:100'],
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['image', 'max:8192'],
        ]);
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
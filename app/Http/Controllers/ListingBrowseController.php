<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ListingBrowseController extends Controller
{
    public function index(Request $request): View
    {
        $query = Listing::published()->with(['business', 'allMedia']);

        if ($type = $request->input('type')) {
            $query->ofType($type);
        }

        if ($city = $request->input('city')) {
            $query->where('city', 'like', "%{$city}%");
        }

        if ($guests = $request->integer('guests')) {
            $query->where(function ($q) use ($guests) {
                $q->where('max_guests', '>=', $guests)
                    ->orWhere('seating_capacity', '>=', $guests);
            });
        }

        if ($maxPrice = $request->input('max_price')) {
            $query->where('base_price', '<=', $maxPrice);
        }

        $listings = $query->latest()->paginate(12)->withQueryString();

        return view('listings.index', [
            'listings' => $listings,
            'types' => Listing::TYPES,
        ]);
    }

    public function show(Listing $listing): View
    {
        abort_unless(
            $listing->isPublished() || (auth()->check() && (
                auth()->user()->isSuperAdmin() || $listing->business->owner_id === auth()->id()
            )),
            404
        );

        $listing->load(['allMedia', 'units' => fn ($q) => $q->where('is_active', true), 'business']);

        $reviews = $listing->reviews()->with('user:id,name')->latest()->paginate(10);

        return view('listings.show', compact('listing', 'reviews'));
    }
}
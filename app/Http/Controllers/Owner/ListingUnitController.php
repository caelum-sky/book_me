<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\ListingUnit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ListingUnitController extends Controller
{
    public function store(Request $request, Listing $listing): RedirectResponse
    {
        $this->authorize('update', $listing);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'capacity' => ['required', 'integer', 'min:1'],
            'price_override' => ['nullable', 'numeric', 'min:0'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $listing->units()->create($validated);

        return back()->with('status', 'Unit added.');
    }

    public function update(Request $request, Listing $listing, ListingUnit $unit): RedirectResponse
    {
        $this->authorize('update', $listing);
        abort_unless($unit->listing_id === $listing->id, 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'capacity' => ['required', 'integer', 'min:1'],
            'price_override' => ['nullable', 'numeric', 'min:0'],
            'quantity' => ['required', 'integer', 'min:1'],
            'is_active' => ['boolean'],
        ]);

        $unit->update($validated);

        return back()->with('status', 'Unit updated.');
    }

    public function destroy(Listing $listing, ListingUnit $unit): RedirectResponse
    {
        $this->authorize('update', $listing);
        abort_unless($unit->listing_id === $listing->id, 404);

        $unit->delete();

        return back()->with('status', 'Unit removed.');
    }
}

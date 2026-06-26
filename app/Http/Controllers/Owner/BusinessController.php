<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Business;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BusinessController extends Controller
{
    public function pendingApproval(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->isApproved()) {
            return to_route('owner.dashboard');
        }

        return view('owner.pending-approval', ['user' => $user]);
    }

    public function create(Request $request): View|RedirectResponse
    {
        if ($request->user()->business) {
            return to_route('owner.business.edit');
        }

        return view('owner.business.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Business::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
        ]);

        $business = Business::create([
            ...$validated,
            'owner_id' => $request->user()->id,
            'slug' => Str::slug($validated['name']).'-'.Str::random(6),
            'status' => 'pending',
        ]);

        ActivityLog::record('business.created', $business);

        return to_route('owner.pending-approval')
            ->with('status', 'Your business profile has been submitted for review.');
    }

    public function edit(Request $request): View
    {
        $business = $request->user()->business()->firstOrFail();
        $this->authorize('view', $business);

        return view('owner.business.edit', compact('business'));
    }

    public function update(Request $request): RedirectResponse
    {
        $business = $request->user()->business()->firstOrFail();
        $this->authorize('update', $business);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'logo' => ['nullable', 'image', 'max:4096'],
            'cover_image' => ['nullable', 'image', 'max:8192'],
            'design_settings' => ['nullable', 'array'],
            'design_settings.theme_color' => ['nullable', 'string', 'max:7'],
            'design_settings.banner_text' => ['nullable', 'string', 'max:255'],
        ]);

        $business->update(
            collect($validated)->except(['logo', 'cover_image'])->all()
        );

        if ($request->hasFile('logo')) {
            $business->setSingleMedia('logo', $request->file('logo'));
        }

        if ($request->hasFile('cover_image')) {
            $business->setSingleMedia('cover', $request->file('cover_image'));
        }

        ActivityLog::record('business.updated', $business);

        return back()->with('status', 'Business profile updated.');
    }
}
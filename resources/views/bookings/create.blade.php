<x-public-layout title="Book {{ $listing->title }}">

<a href="{{ route('listings.show', $listing) }}" style="font-size:12.5px;color:var(--text-tertiary);text-decoration:none;display:inline-flex;align-items:center;gap:5px;margin-bottom:20px;">
    ← Back to listing
</a>

<div style="display:grid;grid-template-columns:1fr 320px;gap:28px;align-items:start;">
    <div>
        <h1 style="font-size:22px;font-weight:700;margin-bottom:4px;">Book {{ $listing->title }}</h1>
        <p style="color:var(--text-secondary);font-size:13px;margin-bottom:22px;">Fill in your details to request a reservation.</p>

        @if($listing->units->isEmpty())
            <div style="background:rgba(251,191,36,.08);border:1px solid rgba(251,191,36,.2);border-radius:var(--radius);padding:14px 16px;font-size:13.5px;color:var(--amber);">
                This listing doesn't have any bookable units available right now.
            </div>
        @else
            <div class="bm-card" style="padding:24px;">
                <form method="POST" action="{{ route('bookings.store', $listing) }}">
                    @csrf

                    <div style="margin-bottom:16px;">
                        <label style="display:block;font-size:12.5px;font-weight:500;color:#b0b8cc;margin-bottom:6px;">
                            {{ $listing->isRestaurant() ? 'Table' : 'Room / Unit' }}
                        </label>
                        <select name="listing_unit_id" required class="bm-input">
                            <option value="">Select an option</option>
                            @foreach($listing->units as $unit)
                                <option value="{{ $unit->id }}" {{ old('listing_unit_id') == $unit->id ? 'selected' : '' }}>
                                    {{ $unit->name }} — up to {{ $unit->capacity }} guests — ₱{{ number_format($unit->effectivePrice(), 0) }}
                                </option>
                            @endforeach
                        </select>
                        @error('listing_unit_id') <div style="font-size:11.5px;color:var(--red);margin-top:4px;">{{ $message }}</div> @enderror
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px;">
                        <div>
                            <label style="display:block;font-size:12.5px;font-weight:500;color:#b0b8cc;margin-bottom:6px;">
                                {{ $listing->isRestaurant() ? 'Date' : 'Check-in' }}
                            </label>
                            <input name="check_in" type="date" required value="{{ old('check_in') }}"
                                   min="{{ now()->toDateString() }}" class="bm-input">
                            @error('check_in') <div style="font-size:11.5px;color:var(--red);margin-top:4px;">{{ $message }}</div> @enderror
                        </div>
                        @if($listing->isRestaurant())
                            <div>
                                <label style="display:block;font-size:12.5px;font-weight:500;color:#b0b8cc;margin-bottom:6px;">Time</label>
                                <input name="reservation_time" type="time" required value="{{ old('reservation_time') }}" class="bm-input">
                                @error('reservation_time') <div style="font-size:11.5px;color:var(--red);margin-top:4px;">{{ $message }}</div> @enderror
                            </div>
                        @else
                            <div>
                                <label style="display:block;font-size:12.5px;font-weight:500;color:#b0b8cc;margin-bottom:6px;">Check-out</label>
                                <input name="check_out" type="date" required value="{{ old('check_out') }}"
                                       min="{{ now()->addDay()->toDateString() }}" class="bm-input">
                                @error('check_out') <div style="font-size:11.5px;color:var(--red);margin-top:4px;">{{ $message }}</div> @enderror
                            </div>
                        @endif
                    </div>

                    <div style="margin-bottom:16px;">
                        <label style="display:block;font-size:12.5px;font-weight:500;color:#b0b8cc;margin-bottom:6px;">Number of guests</label>
                        <input name="guests" type="number" min="1" required value="{{ old('guests', 1) }}" class="bm-input" style="width:140px;">
                        @error('guests') <div style="font-size:11.5px;color:var(--red);margin-top:4px;">{{ $message }}</div> @enderror
                    </div>

                    <div style="margin-bottom:22px;">
                        <label style="display:block;font-size:12.5px;font-weight:500;color:#b0b8cc;margin-bottom:6px;">
                            Special requests <span style="color:var(--text-tertiary);font-weight:400;">(optional)</span>
                        </label>
                        <textarea name="special_requests" rows="3" class="bm-input" placeholder="Allergies, accessibility needs, room preferences…">{{ old('special_requests') }}</textarea>
                        @error('special_requests') <div style="font-size:11.5px;color:var(--red);margin-top:4px;">{{ $message }}</div> @enderror
                    </div>

                    <button type="submit" class="bm-btn primary full">Submit booking request</button>

                    <p style="font-size:12px;color:var(--text-tertiary);text-align:center;margin-top:12px;">
                        Your booking will be pending until the owner confirms it.
                    </p>
                </form>
            </div>
        @endif
    </div>

    {{-- Summary sidebar --}}
    <div>
        <div class="bm-form-card" style="position:sticky;top:80px;">
            @if($listing->primaryImage())
                <img src="{{ $listing->primaryImage()->url() }}" style="width:100%;aspect-ratio:1.4;object-fit:cover;border-radius:10px;margin-bottom:16px;">
            @endif

            <div style="font-size:11.5px;font-weight:600;color:var(--purple);text-transform:uppercase;letter-spacing:.4px;margin-bottom:4px;">{{ $listing->type }}</div>
            <div style="font-size:15px;font-weight:700;margin-bottom:4px;">{{ $listing->title }}</div>
            <div style="font-size:12.5px;color:var(--text-tertiary);margin-bottom:14px;">{{ $listing->business->name }}</div>

            <div style="border-top:1px solid var(--border);margin:14px 0;"></div>

            <div style="display:flex;justify-content:space-between;align-items:baseline;">
                <span style="font-size:13px;color:var(--text-secondary);">From</span>
                <span style="font-size:20px;font-weight:700;">₱{{ number_format($listing->base_price, 0) }}<span style="font-size:12px;color:var(--text-tertiary);font-weight:400;"> / {{ str_replace('per_', '', $listing->price_unit) }}</span></span>
            </div>
        </div>
    </div>
</div>

</x-public-layout>
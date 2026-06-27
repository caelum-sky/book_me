<x-public-layout :title="$listing->title">

    <a href="{{ route('listings.index') }}" style="font-size:12.5px;color:var(--text-tertiary);text-decoration:none;display:inline-block;margin:14px 0;">&larr; Back to search</a>

    @if($listing->gallery()->isNotEmpty())
        <div id="photo-grid" style="display:grid;grid-template-columns:repeat(4,1fr);grid-template-rows:repeat(2,140px);gap:6px;border-radius:16px;overflow:hidden;margin-bottom:24px;cursor:pointer;">
            <div style="grid-column:1/3;grid-row:1/3;" onclick="openLightbox(0)">
                <img src="{{ $listing->gallery()->first()->url() }}" style="width:100%;height:100%;object-fit:cover;" loading="lazy">
            </div>
            @foreach($listing->gallery()->skip(1)->take(4) as $i => $image)
                <div onclick="openLightbox({{ $i + 1 }})" style="position:relative;">
                    <img src="{{ $image->url() }}" style="width:100%;height:100%;object-fit:cover;" loading="lazy">
                    @if($i === 3 && $listing->gallery()->count() > 5)
                        <div style="position:absolute;inset:0;background:rgba(0,0,0,0.5);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:14px;">
                            +{{ $listing->gallery()->count() - 5 }} photos
                        </div>
                    @endif
                </div>
            @endforeach
            @for($i = $listing->gallery()->count(); $i < 5; $i++)
                <div style="background:var(--panel-2);"></div>
            @endfor
        </div>

        {{-- Lightbox overlay --}}
        <div id="lightbox" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.92);z-index:1000;align-items:center;justify-content:center;">
            <button onclick="closeLightbox()" style="position:absolute;top:20px;right:24px;background:none;border:none;color:white;font-size:28px;cursor:pointer;line-height:1;">&times;</button>
            <button onclick="navLightbox(-1)" style="position:absolute;left:24px;top:50%;transform:translateY(-50%);background:rgba(255,255,255,0.1);border:none;color:white;width:44px;height:44px;border-radius:50%;cursor:pointer;font-size:20px;">&larr;</button>
            <img id="lightbox-img" src="" style="max-width:88vw;max-height:84vh;object-fit:contain;border-radius:8px;">
            <button onclick="navLightbox(1)" style="position:absolute;right:24px;top:50%;transform:translateY(-50%);background:rgba(255,255,255,0.1);border:none;color:white;width:44px;height:44px;border-radius:50%;cursor:pointer;font-size:20px;">&rarr;</button>
            <div id="lightbox-counter" style="position:absolute;bottom:24px;left:50%;transform:translateX(-50%);color:rgba(255,255,255,0.7);font-size:13px;"></div>
        </div>

        <script>
            const lightboxImages = @json($listing->gallery()->map(fn($img) => $img->url())->values());
            let lightboxIndex = 0;

            function openLightbox(index) {
                lightboxIndex = index;
                updateLightbox();
                document.getElementById('lightbox').style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }
            function closeLightbox() {
                document.getElementById('lightbox').style.display = 'none';
                document.body.style.overflow = '';
            }
            function navLightbox(dir) {
                lightboxIndex = (lightboxIndex + dir + lightboxImages.length) % lightboxImages.length;
                updateLightbox();
            }
            function updateLightbox() {
                document.getElementById('lightbox-img').src = lightboxImages[lightboxIndex];
                document.getElementById('lightbox-counter').textContent = (lightboxIndex + 1) + ' / ' + lightboxImages.length;
            }
            document.addEventListener('keydown', (e) => {
                if (document.getElementById('lightbox').style.display !== 'flex') return;
                if (e.key === 'Escape') closeLightbox();
                if (e.key === 'ArrowLeft') navLightbox(-1);
                if (e.key === 'ArrowRight') navLightbox(1);
            });
        </script>
    @endif

    <div style="display:grid;grid-template-columns:1fr 360px;gap:48px;">
        <div>
            <div style="font-size:12px;font-weight:600;color:#a08bff;text-transform:uppercase;letter-spacing:0.4px;">{{ $listing->type }}</div>
            <h1 style="font-size:26px;font-weight:700;margin:6px 0 6px;">{{ $listing->title }}</h1>
            <div style="color:var(--text-secondary);font-size:13.5px;">{{ $listing->address ?? $listing->city }}{{ $listing->city ? ', '.$listing->city : '' }}</div>

            @if($listing->reviews_count > 0)
                <div style="display:flex;align-items:center;gap:6px;margin-top:10px;font-size:13.5px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01z"/></svg>
                    <strong>{{ number_format($listing->average_rating, 1) }}</strong>
                    <span style="color:var(--text-tertiary);">({{ $listing->reviews_count }} reviews)</span>
                </div>
            @endif

            <div style="border-top:1px solid var(--border);margin:22px 0;"></div>

            @if($listing->description)
                <p style="font-size:14px;line-height:1.7;color:var(--text-secondary);white-space:pre-line;">{{ $listing->description }}</p>
            @endif

            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(110px,1fr));gap:12px;margin-top:20px;">
                @if($listing->max_guests)
                    <div class="bm-stat-card" style="padding:14px;text-align:center;">
                        <div style="font-size:18px;font-weight:700;">{{ $listing->max_guests }}</div>
                        <div style="font-size:11.5px;color:var(--text-tertiary);">Max guests</div>
                    </div>
                @endif
                @if($listing->bedrooms !== null)
                    <div class="bm-stat-card" style="padding:14px;text-align:center;">
                        <div style="font-size:18px;font-weight:700;">{{ $listing->bedrooms }}</div>
                        <div style="font-size:11.5px;color:var(--text-tertiary);">Bedrooms</div>
                    </div>
                @endif
                @if($listing->bathrooms !== null)
                    <div class="bm-stat-card" style="padding:14px;text-align:center;">
                        <div style="font-size:18px;font-weight:700;">{{ $listing->bathrooms }}</div>
                        <div style="font-size:11.5px;color:var(--text-tertiary);">Bathrooms</div>
                    </div>
                @endif
                @if($listing->seating_capacity)
                    <div class="bm-stat-card" style="padding:14px;text-align:center;">
                        <div style="font-size:18px;font-weight:700;">{{ $listing->seating_capacity }}</div>
                        <div style="font-size:11.5px;color:var(--text-tertiary);">Seats</div>
                    </div>
                @endif
            </div>

            @if($listing->units->isNotEmpty())
                <div style="border-top:1px solid var(--border);margin:24px 0 18px;padding-top:22px;">
                    <h2 style="font-size:16px;font-weight:700;margin:0 0 14px;">{{ $listing->isRestaurant() ? 'Tables' : 'Room availability' }}</h2>
                    @foreach($listing->units as $unit)
                        @php
                            $bookedCount = $unit->bookings()->whereIn('status', ['pending', 'confirmed'])
                                ->where('check_in', '>=', now()->toDateString())->count();
                            $openCount = max(0, $unit->quantity - $bookedCount);
                        @endphp
                        <div class="bm-list-row" style="cursor:default;">
                            <div class="bm-row-info">
                                <div class="name">{{ $unit->name }}</div>
                                <div class="meta">Sleeps {{ $unit->capacity }} &middot; ₱{{ number_format($unit->effectivePrice(), 0) }}</div>
                            </div>
                            <div class="bm-row-badge {{ $openCount > 0 ? 'badge-green' : 'badge-red' }}">
                                {{ $openCount > 0 ? $openCount.' available' : 'Fully booked' }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            @if($listing->amenities)
                <div style="border-top:1px solid var(--border);margin:18px 0;padding-top:22px;">
                    <h2 style="font-size:16px;font-weight:700;margin:0 0 12px;">Amenities</h2>
                    <div style="display:flex;flex-wrap:wrap;gap:8px;">
                        @foreach($listing->amenities as $amenity)
                            <span style="font-size:12.5px;background:var(--panel-2);border:1px solid var(--border-soft);border-radius:999px;padding:7px 14px;">{{ $amenity }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            <div style="border-top:1px solid var(--border);margin:18px 0;padding-top:22px;">
                <h2 style="font-size:16px;font-weight:700;margin:0 0 14px;">Reviews ({{ $listing->reviews_count }})</h2>
                @forelse($reviews as $review)
                    <div style="border-bottom:1px solid var(--border);padding:14px 0;">
                        <div style="display:flex;justify-content:space-between;">
                            <span style="font-size:13.5px;font-weight:600;">{{ $review->user->name }}</span>
                            <span style="font-size:13px;color:var(--text-tertiary);">★ {{ $review->rating }}</span>
                        </div>
                        @if($review->comment)
                            <p style="font-size:13px;color:var(--text-secondary);margin:6px 0 0;">{{ $review->comment }}</p>
                        @endif
                    </div>
                @empty
                    <div style="font-size:13px;color:var(--text-tertiary);">No reviews yet.</div>
                @endforelse
                {{ $reviews->links() }}
            </div>
        </div>

        <div>
            <div class="bm-form-card" style="position:sticky;top:24px;">
                <div style="font-size:22px;font-weight:700;">
                    ₱{{ number_format($listing->base_price, 0) }}
                    <span style="font-size:13px;color:var(--text-tertiary);font-weight:400;">/ {{ str_replace('per_', '', $listing->price_unit) }}</span>
                </div>

                @auth
                    @if(auth()->user()->isCustomer())
                        <a href="{{ route('bookings.create', $listing) }}" class="bm-btn primary full" style="margin-top:16px;">Book now</a>
                    @else
                        <p style="font-size:12px;color:var(--text-tertiary);margin-top:16px;">Only customer accounts can make bookings.</p>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="bm-btn primary full" style="margin-top:16px;">Log in to book</a>
                @endauth

                <div style="border-top:1px solid var(--border);margin:18px 0;"></div>

                <div style="font-size:13.5px;font-weight:600;">{{ $listing->business->name }}</div>
                @if($listing->business->contact_phone)
                    <div style="font-size:12.5px;color:var(--text-tertiary);margin-top:4px;">{{ $listing->business->contact_phone }}</div>
                @endif
            </div>
        </div>
    </div>

</x-public-layout>
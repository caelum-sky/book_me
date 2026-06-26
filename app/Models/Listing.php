<?php

namespace App\Models;

use App\Models\Concerns\HasMedia;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'type', 'title', 'slug', 'description', 'address', 'city', 'province',
    'latitude', 'longitude', 'max_guests', 'bedrooms', 'bathrooms',
    'seating_capacity', 'cuisine_type', 'base_price', 'price_unit',
    'currency', 'amenities', 'design_settings',
    'status', 'rejection_reason', 'is_active', 'average_rating', 'reviews_count',
])]
class Listing extends Model
{
    use HasFactory, SoftDeletes, HasMedia;

    public const TYPES = [
        'dormitory', 'house', 'pad', 'hotel', 'inn', 'motel', 'restaurant',
    ];

    public const ACCOMMODATION_TYPES = [
        'dormitory', 'house', 'pad', 'hotel', 'inn', 'motel',
    ];

    protected function casts(): array
    {
        return [
            'amenities' => 'array',
            'design_settings' => 'array',
            'base_price' => 'decimal:2',
            'average_rating' => 'decimal:2',
            'is_active' => 'boolean',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function units(): HasMany
    {
        return $this->hasMany(ListingUnit::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    // ---------- Gallery convenience ----------

    /**
     * All gallery images for this listing, ordered, as a Collection of
     * Media instances. Use ->url() on each item to render an <img src>.
     *
     * Deliberately named gallery() rather than images() - the old
     * ListingImage relation was accessed as a property (->images,
     * no parens) all over the existing views. Reusing that name as a
     * method would make ->images silently fail instead of erroring
     * clearly, since Eloquent's magic __get would no longer resolve it.
     */
    public function gallery()
    {
        return $this->media('gallery');
    }

    public function primaryImage(): ?Media
    {
        return $this->gallery()->firstWhere('is_primary', true)
            ?? $this->gallery()->first();
    }

    public function isRestaurant(): bool
    {
        return $this->type === 'restaurant';
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')->where('is_active', true);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
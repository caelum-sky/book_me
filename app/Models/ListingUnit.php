<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'capacity', 'price_override', 'quantity', 'is_active'])]
class ListingUnit extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'price_override' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function availabilityBlocks(): HasMany
    {
        return $this->hasMany(AvailabilityBlock::class);
    }

    public function effectivePrice(): float
    {
        return (float) ($this->price_override ?? $this->listing->base_price);
    }

    /**
     * Whether this unit has open capacity for the given date range,
     * accounting for both confirmed/pending bookings and manual blocks.
     * $quantity already-booked units are subtracted from total stock.
     */
    public function isAvailable(string $checkIn, ?string $checkOut = null): bool
    {
        $checkOut ??= $checkIn;

        $blocked = $this->availabilityBlocks()
            ->where('start_date', '<=', $checkOut)
            ->where('end_date', '>=', $checkIn)
            ->exists();

        if ($blocked) {
            return false;
        }

        $overlappingBookings = $this->bookings()
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('check_in', '<', $checkOut)
            ->where(function ($q) use ($checkIn) {
                $q->whereNull('check_out')->orWhere('check_out', '>', $checkIn);
            })
            ->count();

        return $overlappingBookings < $this->quantity;
    }
}
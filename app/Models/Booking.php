<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

#[Fillable([
    'user_id', 'listing_unit_id', 'business_id',
    'check_in', 'check_out', 'reservation_time', 'guests',
    'unit_price', 'quantity', 'total_price', 'currency',
    'special_requests', 'status', 'payment_status',
    'confirmed_at', 'cancelled_at', 'cancellation_reason',
])]
class Booking extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (Booking $booking) {
            $booking->booking_reference ??= 'BK-'.strtoupper(Str::random(8));
        });
    }

    protected function casts(): array
    {
        return [
            'check_in' => 'date',
            'check_out' => 'date',
            'unit_price' => 'decimal:2',
            'total_price' => 'decimal:2',
            'confirmed_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function listingUnit(): BelongsTo
    {
        return $this->belongsTo(ListingUnit::class);
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    public function isCancellable(): bool
    {
        return in_array($this->status, ['pending', 'confirmed'])
            && $this->check_in->isFuture();
    }

    public function scopeUpcoming($query)
    {
        return $query->where('check_in', '>=', now()->toDateString())
            ->whereIn('status', ['pending', 'confirmed']);
    }
}
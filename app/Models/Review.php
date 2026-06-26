<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['rating', 'comment'])]
class Review extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::created(fn (Review $review) => $review->listing->update([
            'average_rating' => $review->listing->reviews()->avg('rating') ?? 0,
            'reviews_count' => $review->listing->reviews()->count(),
        ]));

        static::deleted(fn (Review $review) => $review->listing->update([
            'average_rating' => $review->listing->reviews()->avg('rating') ?? 0,
            'reviews_count' => $review->listing->reviews()->count(),
        ]));
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
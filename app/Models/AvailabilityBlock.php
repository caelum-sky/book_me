<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['start_date', 'end_date', 'reason'])]
class AvailabilityBlock extends Model
{
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function listingUnit(): BelongsTo
    {
        return $this->belongsTo(ListingUnit::class);
    }
}
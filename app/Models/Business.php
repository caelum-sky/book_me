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
    'owner_id', 'name', 'slug', 'description', 'contact_email', 'contact_phone',
    'address', 'city', 'province', 'country', 'latitude', 'longitude',
    'design_settings',
    'status', 'rejection_reason', 'approved_by', 'approved_at', 'is_active',
])]
class Business extends Model
{
    use HasFactory, SoftDeletes, HasMedia;

    protected function casts(): array
    {
        return [
            'design_settings' => 'array',
            'is_active' => 'boolean',
            'approved_at' => 'datetime',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function listings(): HasMany
    {
        return $this->hasMany(Listing::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    // ---------- Logo / cover convenience ----------

    public function logoUrl(): ?string
    {
        return $this->mediaUrl('logo');
    }

    public function coverUrl(): ?string
    {
        return $this->mediaUrl('cover');
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved')->where('is_active', true);
    }
}
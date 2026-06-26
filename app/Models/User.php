<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Concerns\HasMedia;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name', 'email', 'password', 'role', 'phone',
    'approval_status', 'rejection_reason', 'approved_by', 'approved_at',
    'is_active', 'email_verified_at',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasMedia;

    public const ROLE_CUSTOMER = 'customer';

    public const ROLE_BUSINESS_OWNER = 'business_owner';

    public const ROLE_SUPER_ADMIN = 'super_admin';

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'approved_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // ---------- Relationships ----------

    public function business(): HasOne
    {
        return $this->hasOne(Business::class, 'owner_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function otpCodes(): HasMany
    {
        return $this->hasMany(OtpCode::class);
    }

    // ---------- Avatar convenience ----------

    public function avatarUrl(): ?string
    {
        return $this->mediaUrl('avatar');
    }

    // ---------- Role helpers ----------

    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    public function isBusinessOwner(): bool
    {
        return $this->role === self::ROLE_BUSINESS_OWNER;
    }

    public function isCustomer(): bool
    {
        return $this->role === self::ROLE_CUSTOMER;
    }

    public function isApproved(): bool
    {
        return $this->approval_status === 'approved';
    }

    public function isPendingApproval(): bool
    {
        return $this->approval_status === 'pending';
    }

    public function hasFullAccess(): bool
    {
        if (! $this->hasVerifiedEmail()) {
            return false;
        }

        if ($this->isBusinessOwner()) {
            return $this->isApproved();
        }

        return true;
    }
}
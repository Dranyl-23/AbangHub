<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = ['username', 'email', 'password', 'full_name', 'phone', 'user_type', 'profile_image', 'google_id', 'is_verified', 'is_banned', 'id_picture', 'emergency_contact_name', 'emergency_contact_phone'];

    protected $hidden = ['password', 'remember_token'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_verified' => 'boolean',
            'is_banned' => 'boolean',
        ];
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class, 'owner_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(LandlordDocument::class);
    }

    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class);
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function isAdmin(): bool
    {
        return $this->user_type === 'admin';
    }

    public function isTenant(): bool
    {
        return $this->user_type === 'tenant';
    }

    public function isLandlord(): bool
    {
        return $this->user_type === 'landlord';
    }

    public function favorites(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Property::class, 'favorites', 'user_id', 'property_id')->withTimestamps();
    }

    public function leases(): HasMany
    {
        return $this->hasMany(Lease::class, 'tenant_id');
    }

    public function propertyReviews(): HasMany
    {
        return $this->hasMany(Review::class, 'tenant_id');
    }

    public function tenantReviewsGiven(): HasMany
    {
        return $this->hasMany(TenantReview::class, 'landlord_id');
    }

    public function tenantReviewsReceived(): HasMany
    {
        return $this->hasMany(TenantReview::class, 'tenant_id');
    }

    public function getAverageTenantRatingAttribute(): float
    {
        return (float) ($this->tenantReviewsReceived->avg('rating') ?? 0);
    }

    public function getAverageLandlordRatingAttribute(): float
    {
        // Average of all reviews on their properties
        return (float) (Review::whereIn('property_id', $this->properties()->pluck('id'))->avg('rating') ?? 0);
    }
}

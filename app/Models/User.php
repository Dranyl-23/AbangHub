<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['username', 'email', 'password', 'full_name', 'phone', 'user_type', 'profile_image', 'google_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

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
}

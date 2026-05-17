<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, BelongsToTenant, HasFactory, HasRoles, Notifiable, TwoFactorAuthenticatable;

    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'user_type',
        'password',
        'profile_photo_path',
        'phone',
        'dob',
        'address',
        'preferences',
        'google_id',
        'google_token',
        'facebook_id',
        'facebook_token',
        'github_id',
        'github_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'google_token',
        'facebook_token',
        'github_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'dob' => 'date',
        'preferences' => 'array',
    ];

    // ── Profile photo URL ─────────────────────────────────────
    public function getProfilePhotoUrlAttribute(): string
    {
        if ($this->profile_photo_path) {
            return Storage::disk('public')->url($this->profile_photo_path);
        }
        // Gravatar fallback
        $hash = md5(strtolower(trim($this->email)));

        return "https://www.gravatar.com/avatar/{$hash}?d=mp&s=200";
    }

    public function getInitialsAttribute(): string
    {
        $name = trim($this->name ?? '');
        if (!$name) {
            return 'U';
        }

        $parts = explode(' ', $name);
        $parts = array_filter($parts);

        if (count($parts) >= 2) {
            $first = $parts[0];
            $last = end($parts);
            return strtoupper($first[0] . $last[0]);
        }

        return strtoupper($parts[0][0] ?? 'U');
    }

    // ── Social connections helper ─────────────────────────────
    public function isConnected(string $provider): bool
    {
        return match ($provider) {
            'google' => ! empty($this->google_id),
            'facebook' => ! empty($this->facebook_id),
            'github' => ! empty($this->github_id),
            default => false,
        };
    }

    public function disconnectSocial(string $provider): void
    {
        match ($provider) {
            'google' => $this->update(['google_id' => null,   'google_token' => null]),
            'facebook' => $this->update(['facebook_id' => null, 'facebook_token' => null]),
            'github' => $this->update(['github_id' => null,   'github_token' => null]),
        };
    }

    // ── Preferences helpers ───────────────────────────────────
    public function getPref(string $key, mixed $default = null): mixed
    {
        return $this->preferences[$key] ?? $default;
    }

    // ── Relations ─────────────────────────────────────────────
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function merchant()
    {
        return $this->hasOne(Merchant::class);
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class)->withTimestamps();
    }
}

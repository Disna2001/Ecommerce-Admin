<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Tenant extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'primary_domain',
        'domains',
        'status',
        'is_default',
        'data',
    ];

    protected $casts = [
        'domains' => 'array',
        'data' => 'array',
        'is_default' => 'boolean',
    ];

    public function deployment(): HasOne
    {
        return $this->hasOne(Deployment::class);
    }

    public function matchesHost(?string $host): bool
    {
        $normalized = static::normalizeHost($host);

        if (!$normalized) {
            return false;
        }

        $domains = collect($this->domains ?? [])
            ->push($this->primary_domain)
            ->filter()
            ->map(fn (string $domain) => static::normalizeHost($domain));

        return $domains->contains($normalized);
    }

    public static function normalizeHost(?string $host): ?string
    {
        if (!filled($host)) {
            return null;
        }

        $normalized = strtolower(trim((string) $host));

        if (str_contains($normalized, '://')) {
            $normalized = (string) parse_url($normalized, PHP_URL_HOST);
        }

        if (str_contains($normalized, ':')) {
            [$normalized] = explode(':', $normalized, 2);
        }

        return $normalized ?: null;
    }
}

<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Services\Tenancy\TenantManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

class SiteSetting extends Model
{
    use BelongsToTenant;

    protected $fillable = ['tenant_id', 'key', 'value', 'type', 'group', 'label'];

    protected const STOREFRONT_CACHE_KEYS = [
        'storefront_shared_layout_data',
        'home_latest_reviews',
        'product_list_categories',
        'product_list_brands',
    ];

    protected const SECRET_KEYS = [
        'mail_smtp_password',
        'mail_api_key',
        'mail_api_secret',
        'whatsapp_api_key',
        'whatsapp_webhook_verify_token',
        'ai_api_key',
        'custom_integrations_api_key',
        'payhere_merchant_secret',
    ];

    // -------------------------------------------------------------------------
    // Static helpers
    // -------------------------------------------------------------------------

    /**
     * Get a single setting value by key.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $tenantId = static::resolvedTenantId();
        $cacheKey = 'tenant_' . ($tenantId ?: 'platform') . "_site_setting_{$key}";

        try {
            $setting = Cache::remember($cacheKey, 600, function () use ($key, $tenantId) {
                return static::query()
                    ->when($tenantId, fn ($query) => $query->where('tenant_id', $tenantId))
                    ->where('key', $key)
                    ->first();
            });
        } catch (\Throwable) {
            try {
                $setting = static::query()
                    ->when($tenantId, fn ($query) => $query->where('tenant_id', $tenantId))
                    ->where('key', $key)
                    ->first();
            } catch (\Throwable) {
                return $default;
            }
        }

        if (!$setting) return $default;

        $value = match ($setting->type) {
            'boolean' => (bool) $setting->value,
            'json'    => is_array($setting->value)
                            ? $setting->value
                            : (json_decode($setting->value, true) ?? []),
            default   => $setting->value,
        };

        return static::isSecretKey($key)
            ? static::decryptSecretValue($value, $default)
            : $value;
    }

    /**
     * Set a setting value by key (upsert).
     */
    public static function set(string $key, mixed $value, string $type = 'text', string $group = 'general', string $label = ''): void
    {
        $tenantId = static::resolvedTenantId();

        if (static::isSecretKey($key) && filled($value)) {
            $value = static::encryptSecretValue((string) $value);
        }

        static::updateOrCreate(
            ['tenant_id' => $tenantId, 'key' => $key],
            [
                'tenant_id' => $tenantId,
                'value' => is_array($value) ? json_encode($value) : $value,
                'type'  => $type,
                'group' => $group,
                'label' => $label,
            ]
        );

        try {
            Cache::forget('tenant_' . ($tenantId ?: 'platform') . "_site_setting_{$key}");
        } catch (\Throwable) {
            // Ignore cache driver failures and keep the DB write as the source of truth.
        }
        static::invalidateRelatedCaches($key);
    }

    /**
     * Get all settings for a group as key=>value array.
     */
    public static function getGroup(string $group): array
    {
        try {
            return static::query()
                ->when(static::resolvedTenantId(), fn ($query, $tenantId) => $query->where('tenant_id', $tenantId))
                ->where('group', $group)
                ->get()
                ->mapWithKeys(fn($s) => [$s->key => static::get($s->key)])
                ->toArray();
        } catch (\Throwable) {
            return [];
        }
    }

    protected static function isSecretKey(string $key): bool
    {
        return in_array($key, static::SECRET_KEYS, true);
    }

    protected static function encryptSecretValue(string $value): string
    {
        if (str_starts_with($value, 'enc::')) {
            return $value;
        }

        return 'enc::' . Crypt::encryptString($value);
    }

    protected static function decryptSecretValue(mixed $value, mixed $default = null): mixed
    {
        if (!is_string($value) || $value === '') {
            return $value;
        }

        if (!str_starts_with($value, 'enc::')) {
            return $value;
        }

        try {
            return Crypt::decryptString(substr($value, 5));
        } catch (\Throwable) {
            return $default;
        }
    }

    protected static function invalidateRelatedCaches(string $key): void
    {
        foreach (self::STOREFRONT_CACHE_KEYS as $cacheKey) {
            try {
                Cache::forget($cacheKey);
            } catch (\Throwable) {
                // Ignore cache driver failures and allow storefront reads to fallback.
            }
        }

        if ($key === 'featured_product_ids' || $key === 'new_arrivals_ids' || $key === 'deal_product_ids') {
            try {
                Cache::forget('storefront_shared_layout_data');
            } catch (\Throwable) {
                // Ignore cache driver failures and allow storefront reads to fallback.
            }
        }
    }

    protected static function resolvedTenantId(): ?int
    {
        $tenantManager = app(TenantManager::class);

        return $tenantManager->currentId() ?: $tenantManager->default()?->id;
    }
}

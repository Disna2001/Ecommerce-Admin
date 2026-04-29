<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Services\Tenancy\TenantManager;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Team extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'tenant_id',
        'name',
        'slug',
        'description',
        'color',
        'is_active',
        'default_role_name',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public static function ensureDefaultTeamsForCurrentTenant(): Collection
    {
        $tenantId = app(TenantManager::class)->currentId();

        return static::ensureDefaultTeams($tenantId);
    }

    public static function ensureDefaultTeams(?int $tenantId = null): Collection
    {
        $defaults = collect([
            [
                'name' => 'Store Management',
                'slug' => 'store-management',
                'description' => 'Owns storefront visibility, pricing presentation, banners, and merchandising.',
                'color' => 'sky',
                'default_role_name' => null,
            ],
            [
                'name' => 'Warehouse Management',
                'slug' => 'warehouse-management',
                'description' => 'Handles stock intake, stock counts, inventory corrections, and storage readiness.',
                'color' => 'amber',
                'default_role_name' => null,
            ],
            [
                'name' => 'Site Management',
                'slug' => 'site-management',
                'description' => 'Maintains catalog publishing, site settings, navigation, and presentation polish.',
                'color' => 'violet',
                'default_role_name' => null,
            ],
            [
                'name' => 'Order Management',
                'slug' => 'order-management',
                'description' => 'Processes new orders, updates order states, tracks fulfilment, and reviews returns.',
                'color' => 'emerald',
                'default_role_name' => null,
            ],
            [
                'name' => 'POS Team',
                'slug' => 'pos-team',
                'description' => 'Runs in-person checkout, counter billing, and quick walk-in customer operations.',
                'color' => 'rose',
                'default_role_name' => null,
            ],
        ]);

        $created = collect();

        foreach ($defaults as $team) {
            $created->push(static::withoutGlobalScopes()->firstOrCreate(
                [
                    'tenant_id' => $tenantId,
                    'slug' => $team['slug'],
                ],
                $team
            ));
        }

        return $created;
    }

    protected static function booted(): void
    {
        static::saving(function (Team $team) {
            if (blank($team->slug)) {
                $team->slug = Str::slug($team->name);
            }
        });
    }
}

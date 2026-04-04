<?php

namespace App\Services\Tenancy;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class TenantManager
{
    protected ?Tenant $tenant = null;

    public function current(): ?Tenant
    {
        return $this->tenant;
    }

    public function currentId(): ?int
    {
        return $this->tenant?->id;
    }

    public function setCurrent(?Tenant $tenant): void
    {
        $this->tenant = $tenant;
    }

    public function initializeFromRequest(Request $request): ?Tenant
    {
        if ($this->tenant) {
            return $this->tenant;
        }

        if (!$this->canResolveTenants()) {
            return null;
        }

        $tenant = $this->resolveForHost($request->getHost());

        if (!$tenant && $this->isLocalHost($request->getHost())) {
            $tenant = $this->default();
        }

        $this->tenant = $tenant;

        return $tenant;
    }

    public function resolveForHost(?string $host): ?Tenant
    {
        if (!$this->canResolveTenants()) {
            return null;
        }

        $normalizedHost = Tenant::normalizeHost($host);

        if (!$normalizedHost) {
            return null;
        }

        return Tenant::query()
            ->where('status', 'active')
            ->get()
            ->first(fn (Tenant $tenant) => $tenant->matchesHost($normalizedHost));
    }

    public function default(): ?Tenant
    {
        if (!$this->canResolveTenants()) {
            return null;
        }

        return Tenant::query()
            ->where('status', 'active')
            ->orderByDesc('is_default')
            ->orderBy('id')
            ->first();
    }

    public function scopedCacheKey(string $key): string
    {
        return 'tenant_' . ($this->currentId() ?: 'default') . '_' . $key;
    }

    protected function canResolveTenants(): bool
    {
        try {
            return Schema::hasTable('tenants');
        } catch (\Throwable) {
            return false;
        }
    }

    protected function isLocalHost(?string $host): bool
    {
        return in_array(Tenant::normalizeHost($host), ['localhost', '127.0.0.1'], true);
    }
}

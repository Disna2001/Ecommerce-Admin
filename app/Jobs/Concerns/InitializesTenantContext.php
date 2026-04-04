<?php

namespace App\Jobs\Concerns;

use App\Models\Tenant;
use App\Services\Tenancy\TenantManager;

trait InitializesTenantContext
{
    protected function initializeTenantContext(?int $tenantId): void
    {
        if (!$tenantId) {
            return;
        }

        $tenant = Tenant::query()->find($tenantId);

        if ($tenant) {
            app(TenantManager::class)->setCurrent($tenant);
        }
    }
}

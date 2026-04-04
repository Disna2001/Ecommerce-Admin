<?php

namespace App\Http\Middleware;

use App\Services\Tenancy\TenantManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveTenant
{
    public function __construct(
        protected TenantManager $tenantManager
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $tenant = $this->tenantManager->initializeFromRequest($request);

        if (!$tenant) {
            abort(404, 'Tenant not found for this hostname.');
        }

        $request->attributes->set('tenant', $tenant);

        return $next($request);
    }
}

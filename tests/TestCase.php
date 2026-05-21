<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (\Illuminate\Support\Facades\Schema::hasTable('tenants')) {
            $tenant = \App\Models\Tenant::firstOrCreate(
                ['slug' => 'default'],
                [
                    'name' => 'Default Tenant',
                    'primary_domain' => 'localhost',
                    'domains' => ['127.0.0.1'],
                    'status' => 'active',
                    'is_default' => true,
                ]
            );
            app(\App\Services\Tenancy\TenantManager::class)->setCurrent($tenant);
        }
    }
}

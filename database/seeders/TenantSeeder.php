<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        $appUrl = config('app.url', env('APP_URL', 'http://localhost'));
        $host = parse_url($appUrl, PHP_URL_HOST) ?: 'localhost';

        Tenant::updateOrCreate(
            ['primary_domain' => $host],
            [
                'name' => config('app.name', 'Default Tenant'),
                'slug' => Str::slug(config('app.name', 'default-tenant')) ?: 'default-tenant',
                'domains' => array_values(array_unique([$host, 'localhost', '127.0.0.1'])),
                'status' => 'active',
                'is_default' => true,
                'data' => [],
            ]
        );
    }
}

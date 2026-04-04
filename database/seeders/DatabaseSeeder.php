<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            TenantSeeder::class,
            RoleSeeder::class,
            SiteSettingSeeder::class,
            AdminSeeder::class, // This will create the first admin
        ]);
    }
}

<?php

namespace Database\Seeders;

use App\Models\StorefrontPage;
use Illuminate\Database\Seeder;

class StorefrontPageSeeder extends Seeder
{
    public function run(): void
    {
        StorefrontPage::updateOrCreate(
            ['key' => 'home'],
            ['label' => 'Home']
        );
    }
}

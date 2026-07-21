<?php

namespace Database\Seeders;

use App\Models\StorefrontPage;
use App\Models\StorefrontSection;
use App\Services\Storefront\SectionRegistry;
use Illuminate\Database\Seeder;

class StorefrontSectionSeeder extends Seeder
{
    public function run(SectionRegistry $registry): void
    {
        $homePage = StorefrontPage::firstOrCreate(
            ['key' => 'home'],
            ['label' => 'Home']
        );

        $types = ['hero', 'banner-rail', 'featured-products', 'footer'];

        foreach ($types as $order => $type) {
            $contract = $registry->get($type);
            $defaults = $contract ? $contract->defaults() : [];

            StorefrontSection::updateOrCreate(
                [
                    'page_id' => $homePage->id,
                    'type' => $type,
                ],
                [
                    'order' => $order,
                    'is_active' => true,
                    'config' => $defaults,
                    'style' => [],
                    'schema_version' => 1,
                    'slot' => 'before',
                ]
            );
        }
    }
}

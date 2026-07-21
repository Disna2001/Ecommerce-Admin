<?php

namespace App\Services\Storefront\Sections;

use App\Services\Storefront\SectionContract;

class BannerRailSection implements SectionContract
{
    public function key(): string
    {
        return 'banner-rail';
    }

    public function label(): string
    {
        return 'Promo Banner Rail';
    }

    public function bladeView(): string
    {
        return 'storefront.sections.banner-rail';
    }

    public function schema(): array
    {
        return [
            [
                'name' => 'title',
                'label' => 'Rail Header',
                'type' => 'text',
                'default' => 'Promotional Highlights',
            ],
            [
                'name' => 'items',
                'label' => 'Banner Rail Items',
                'type' => 'repeater',
                'fields' => [
                    ['name' => 'title', 'label' => 'Title', 'type' => 'text', 'default' => 'Special Deal'],
                    ['name' => 'subtitle', 'label' => 'Subtitle', 'type' => 'text', 'default' => 'Limited Stock'],
                    ['name' => 'image', 'label' => 'Banner Image', 'type' => 'image', 'default' => ''],
                    ['name' => 'link', 'label' => 'Link URL', 'type' => 'text', 'default' => '/products'],
                    ['name' => 'link_label', 'label' => 'Link Label', 'type' => 'text', 'default' => 'Explore Deal'],
                ],
                'default' => [
                    ['title' => 'Fast Express Shipping', 'subtitle' => 'Island-wide delivery within 24h', 'image' => '', 'link' => '/products', 'link_label' => 'Shop Now'],
                    ['title' => 'Official Warranty', 'subtitle' => 'Guaranteed genuine parts', 'image' => '', 'link' => '/warranties', 'link_label' => 'Learn More'],
                ],
            ],
        ];
    }

    public function defaults(): array
    {
        $defaults = [];
        foreach ($this->schema() as $field) {
            $defaults[$field['name']] = $field['default'] ?? null;
        }
        return $defaults;
    }
}

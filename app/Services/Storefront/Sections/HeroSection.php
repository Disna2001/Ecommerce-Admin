<?php

namespace App\Services\Storefront\Sections;

use App\Services\Storefront\SectionContract;

class HeroSection implements SectionContract
{
    public function key(): string
    {
        return 'hero';
    }

    public function label(): string
    {
        return 'Marketplace Hero & Promo Tiles';
    }

    public function bladeView(): string
    {
        return 'storefront.sections.hero';
    }

    public function schema(): array
    {
        return [
            [
                'name' => 'heading',
                'label' => 'Main Hero Title',
                'type' => 'text',
                'default' => 'Genuine Smartphone Displays & Parts',
            ],
            [
                'name' => 'subheading',
                'label' => 'Main Hero Subtitle',
                'type' => 'text',
                'default' => 'Original display assemblies, replacement screens, and electronic components.',
            ],
            [
                'name' => 'cta_text',
                'label' => 'Main Button Text',
                'type' => 'text',
                'default' => 'Shop Collection',
            ],
            [
                'name' => 'cta_url',
                'label' => 'Main Button Link URL',
                'type' => 'text',
                'default' => '/products',
            ],
            [
                'name' => 'bg_color',
                'label' => 'Background Accent Color',
                'type' => 'color',
                'default' => '#7c3aed',
            ],
            [
                'name' => 'hero_image',
                'label' => 'Hero Image URL',
                'type' => 'image',
                'default' => '',
            ],
            [
                'name' => 'tile1_title',
                'label' => 'Side Tile 1 Title',
                'type' => 'text',
                'default' => 'New Arrivals',
            ],
            [
                'name' => 'tile1_subtitle',
                'label' => 'Side Tile 1 Subtitle',
                'type' => 'text',
                'default' => 'Fresh display assemblies in stock',
            ],
            [
                'name' => 'tile1_link',
                'label' => 'Side Tile 1 Link',
                'type' => 'text',
                'default' => '/products?sort=newest',
            ],
            [
                'name' => 'tile1_badge',
                'label' => 'Side Tile 1 Badge',
                'type' => 'text',
                'default' => 'New In',
            ],
            [
                'name' => 'tile2_title',
                'label' => 'Side Tile 2 Title',
                'type' => 'text',
                'default' => 'Best Sellers',
            ],
            [
                'name' => 'tile2_subtitle',
                'label' => 'Side Tile 2 Subtitle',
                'type' => 'text',
                'default' => 'Top rated replacement modules',
            ],
            [
                'name' => 'tile2_link',
                'label' => 'Side Tile 2 Link',
                'type' => 'text',
                'default' => '/products',
            ],
            [
                'name' => 'tile2_badge',
                'label' => 'Side Tile 2 Badge',
                'type' => 'text',
                'default' => 'Hot',
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

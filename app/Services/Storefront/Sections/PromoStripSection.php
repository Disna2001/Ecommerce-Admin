<?php

namespace App\Services\Storefront\Sections;

use App\Services\Storefront\SectionContract;

class PromoStripSection implements SectionContract
{
    public function key(): string
    {
        return 'promo-strip';
    }

    public function label(): string
    {
        return 'Promotional Strip';
    }

    public function bladeView(): string
    {
        return 'storefront.sections.promo-strip';
    }

    public function schema(): array
    {
        return [
            [
                'name' => 'badge',
                'label' => 'Badge Text',
                'type' => 'text',
                'default' => 'Island-Wide Delivery',
            ],
            [
                'name' => 'title',
                'label' => 'Title Text',
                'type' => 'text',
                'default' => 'Quality Smartphone Displays & Parts Across Sri Lanka',
            ],
            [
                'name' => 'text',
                'label' => 'Body Text',
                'type' => 'text',
                'default' => 'Genuine smartphone display assemblies and electronics parts delivered to your doorstep. Track your order in real time.',
            ],
            [
                'name' => 'button_text',
                'label' => 'Button Text',
                'type' => 'text',
                'default' => 'Shop Now',
            ],
            [
                'name' => 'button_link',
                'label' => 'Button Link URL',
                'type' => 'text',
                'default' => '/products',
            ],
            [
                'name' => 'bg_from',
                'label' => 'Background Color From',
                'type' => 'color',
                'default' => '#0f172a',
            ],
            [
                'name' => 'bg_to',
                'label' => 'Background Color To',
                'type' => 'color',
                'default' => '#334155',
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

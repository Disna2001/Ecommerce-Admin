<?php

namespace App\Services\Storefront\Sections;

use App\Services\Storefront\SectionContract;

class DealsSection implements SectionContract
{
    public function key(): string
    {
        return 'deals';
    }

    public function label(): string
    {
        return 'Flash Deals & Best Sellers Grid';
    }

    public function bladeView(): string
    {
        return 'storefront.sections.deals';
    }

    public function schema(): array
    {
        return [
            [
                'name' => 'title',
                'label' => 'Section Title',
                'type' => 'text',
                'default' => 'Flash Deals & Best Sellers',
            ],
            [
                'name' => 'subtitle',
                'label' => 'Section Description',
                'type' => 'text',
                'default' => 'Special prices on top smartphone display modules.',
            ],
            [
                'name' => 'limit',
                'label' => 'Number of Items to Show',
                'type' => 'text',
                'default' => '6',
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

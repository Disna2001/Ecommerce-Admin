<?php

namespace App\Services\Storefront\Sections;

use App\Services\Storefront\SectionContract;

class FeaturedProductsSection implements SectionContract
{
    public function key(): string
    {
        return 'featured-products';
    }

    public function label(): string
    {
        return 'Featured Products Grid';
    }

    public function bladeView(): string
    {
        return 'storefront.sections.featured-products';
    }

    public function schema(): array
    {
        return [
            [
                'name' => 'title',
                'label' => 'Section Title',
                'type' => 'text',
                'default' => 'Featured Display Modules',
            ],
            [
                'name' => 'subtitle',
                'label' => 'Section Description',
                'type' => 'text',
                'default' => 'Hand-picked top tier replacement displays and touch assemblies.',
            ],
            [
                'name' => 'limit',
                'label' => 'Number of Items to Show',
                'type' => 'text',
                'default' => '8',
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

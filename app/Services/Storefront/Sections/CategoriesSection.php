<?php

namespace App\Services\Storefront\Sections;

use App\Models\Category;
use App\Services\Storefront\SectionContract;

class CategoriesSection implements SectionContract
{
    public function key(): string
    {
        return 'categories';
    }

    public function label(): string
    {
        return 'Shop by Category';
    }

    public function bladeView(): string
    {
        return 'storefront.sections.categories';
    }

    public function schema(): array
    {
        return [
            [
                'name' => 'title',
                'label' => 'Section Title',
                'type' => 'text',
                'default' => 'Shop by Category',
            ],
            [
                'name' => 'subtitle',
                'label' => 'Section Subtitle',
                'type' => 'text',
                'default' => 'Jump straight into the product family you need.',
            ],
            [
                'name' => 'see_all_label',
                'label' => 'See All Link Text',
                'type' => 'text',
                'default' => 'See All',
            ],
            [
                'name' => 'all_label',
                'label' => 'Full Catalog Label',
                'type' => 'text',
                'default' => 'Full Catalog',
            ],
            [
                'name' => 'all_sub_label',
                'label' => 'Full Catalog Sub-label',
                'type' => 'text',
                'default' => 'All Products',
            ],
            [
                'name' => 'classification_label',
                'label' => 'Category Classification Label',
                'type' => 'text',
                'default' => 'Category',
            ],
            [
                'name' => 'show_icons',
                'label' => 'Show Category Icons',
                'type' => 'boolean',
                'default' => true,
            ],
            [
                'name' => 'limit',
                'label' => 'Number of Categories to Show',
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

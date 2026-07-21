<?php

namespace App\Services\Storefront\Sections;

use App\Services\Storefront\SectionContract;

class NewArrivalsSection implements SectionContract
{
    public function key(): string
    {
        return 'new-arrivals';
    }

    public function label(): string
    {
        return 'New Arrivals Grid';
    }

    public function bladeView(): string
    {
        return 'storefront.sections.new-arrivals';
    }

    public function schema(): array
    {
        return [
            [
                'name' => 'title',
                'label' => 'Section Title',
                'type' => 'text',
                'default' => 'New Arrivals',
            ],
            [
                'name' => 'subtitle',
                'label' => 'Section Description',
                'type' => 'text',
                'default' => 'Fresh products customers should notice first.',
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

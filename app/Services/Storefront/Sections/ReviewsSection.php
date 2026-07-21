<?php

namespace App\Services\Storefront\Sections;

use App\Models\Review;
use App\Services\Storefront\SectionContract;

class ReviewsSection implements SectionContract
{
    public function key(): string
    {
        return 'reviews';
    }

    public function label(): string
    {
        return 'Customer Reviews';
    }

    public function bladeView(): string
    {
        return 'storefront.sections.reviews';
    }

    public function schema(): array
    {
        return [
            [
                'name' => 'eyebrow',
                'label' => 'Eyebrow Text',
                'type' => 'text',
                'default' => 'Testimonials',
            ],
            [
                'name' => 'title',
                'label' => 'Section Title',
                'type' => 'text',
                'default' => 'What Customers Say',
            ],
            [
                'name' => 'subtitle',
                'label' => 'Section Subtitle',
                'type' => 'text',
                'default' => 'Trusted by customers across Sri Lanka',
            ],
            [
                'name' => 'limit',
                'label' => 'Number of Reviews to Show',
                'type' => 'text',
                'default' => '3',
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

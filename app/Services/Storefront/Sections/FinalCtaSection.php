<?php

namespace App\Services\Storefront\Sections;

use App\Services\Storefront\SectionContract;

class FinalCtaSection implements SectionContract
{
    public function key(): string
    {
        return 'final-cta';
    }

    public function label(): string
    {
        return 'Final Call to Action';
    }

    public function bladeView(): string
    {
        return 'storefront.sections.final-cta';
    }

    public function schema(): array
    {
        return [
            [
                'name' => 'eyebrow',
                'label' => 'Eyebrow Text',
                'type' => 'text',
                'default' => 'Summer Sale',
            ],
            [
                'name' => 'title',
                'label' => 'Heading Title',
                'type' => 'text',
                'default' => 'Don\'t miss out — shop the collection now!',
            ],
            [
                'name' => 'subtitle',
                'label' => 'Subtitle Text',
                'type' => 'text',
                'default' => 'Fast Sri Lanka-wide delivery & genuine quality guarantee.',
            ],
            [
                'name' => 'button_text',
                'label' => 'Button Text',
                'type' => 'text',
                'default' => 'Browse Store',
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
                'default' => '#6d28d9',
            ],
            [
                'name' => 'bg_to',
                'label' => 'Background Color To',
                'type' => 'color',
                'default' => '#7c3aed',
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

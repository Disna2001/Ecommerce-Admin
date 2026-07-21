<?php

namespace App\Services\Storefront\Sections;

use App\Services\Storefront\SectionContract;

class FooterSection implements SectionContract
{
    public function key(): string
    {
        return 'footer';
    }

    public function label(): string
    {
        return 'Storefront Footer';
    }

    public function bladeView(): string
    {
        return 'storefront.sections.footer';
    }

    public function schema(): array
    {
        return [
            [
                'name' => 'brand_name',
                'label' => 'Brand Name',
                'type' => 'text',
                'default' => 'DISPLAY LANKA.LK',
            ],
            [
                'name' => 'about_text',
                'label' => 'About Store Summary',
                'type' => 'text',
                'default' => 'Sri Lanka\'s leading importer and distributor of authentic smartphone display assemblies and touch panels.',
            ],
            [
                'name' => 'contact_email',
                'label' => 'Contact Email',
                'type' => 'text',
                'default' => 'support@displaylanka.lk',
            ],
            [
                'name' => 'contact_phone',
                'label' => 'Hotline Phone',
                'type' => 'text',
                'default' => '+94 77 123 4567',
            ],
            [
                'name' => 'copyright_text',
                'label' => 'Copyright Line',
                'type' => 'text',
                'default' => '© 2026 Display Lanka LK. All rights reserved.',
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

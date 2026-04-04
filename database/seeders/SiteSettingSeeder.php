<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SiteSetting;
use App\Models\Tenant;

class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::query()->where('is_default', true)->first() ?? Tenant::query()->first();

        if (!$tenant) {
            $this->command?->warn('No tenant found. Skipping site settings seed.');
            return;
        }

        $defaults = [
            // ── Branding ──────────────────────────────────────────────
            ['key' => 'site_name',        'value' => 'DISPLAY LANKA.LK',                          'type' => 'text',    'group' => 'branding',    'label' => 'Site Name'],
            ['key' => 'site_tagline',     'value' => 'Your one-stop shop for everything trendy.',  'type' => 'text',    'group' => 'branding',    'label' => 'Site Tagline'],
            ['key' => 'logo_path',        'value' => '',                                           'type' => 'image',   'group' => 'branding',    'label' => 'Logo'],
            ['key' => 'favicon_path',     'value' => '',                                           'type' => 'image',   'group' => 'branding',    'label' => 'Favicon'],

            // ── Colors ────────────────────────────────────────────────
            ['key' => 'primary_color',    'value' => '#4f46e5',  'type' => 'color', 'group' => 'appearance', 'label' => 'Primary Color'],
            ['key' => 'secondary_color',  'value' => '#7c3aed',  'type' => 'color', 'group' => 'appearance', 'label' => 'Secondary Color'],
            ['key' => 'accent_color',     'value' => '#06b6d4',  'type' => 'color', 'group' => 'appearance', 'label' => 'Accent Color'],
            ['key' => 'text_color',       'value' => '#111827',  'type' => 'color', 'group' => 'appearance', 'label' => 'Body Text Color'],
            ['key' => 'bg_color',         'value' => '#f9fafb',  'type' => 'color', 'group' => 'appearance', 'label' => 'Page Background'],
            ['key' => 'nav_bg_color',     'value' => '#ffffff',  'type' => 'color', 'group' => 'appearance', 'label' => 'Navigation Background'],

            // ── Top Bar ───────────────────────────────────────────────
            ['key' => 'topbar_enabled',   'value' => '1',                                         'type' => 'boolean', 'group' => 'appearance', 'label' => 'Show Top Bar'],
            ['key' => 'topbar_text',      'value' => 'Summer Sale! Up to 50% off selected items.','type' => 'text',    'group' => 'appearance', 'label' => 'Top Bar Text'],
            ['key' => 'topbar_bg_from',   'value' => '#7c3aed',  'type' => 'color', 'group' => 'appearance', 'label' => 'Top Bar Gradient From'],
            ['key' => 'topbar_bg_to',     'value' => '#4f46e5',  'type' => 'color', 'group' => 'appearance', 'label' => 'Top Bar Gradient To'],

            // ── Hero ──────────────────────────────────────────────────
            ['key' => 'hero_title',       'value' => 'Summer Sale',                               'type' => 'text',  'group' => 'hero', 'label' => 'Hero Title'],
            ['key' => 'hero_subtitle',    'value' => 'Get up to 50% off on trending items',       'type' => 'text',  'group' => 'hero', 'label' => 'Hero Subtitle'],
            ['key' => 'hero_button_text', 'value' => 'Shop Now',                                  'type' => 'text',  'group' => 'hero', 'label' => 'Hero Button Text'],
            ['key' => 'hero_button_link', 'value' => '#',                                         'type' => 'text',  'group' => 'hero', 'label' => 'Hero Button Link'],
            ['key' => 'hero_highlight_text', 'value' => 'Unbeatable Prices',                      'type' => 'text',  'group' => 'hero', 'label' => 'Hero Highlight Text'],
            ['key' => 'hero_microcopy',   'value' => 'Delivered to your inbox in seconds.',       'type' => 'text',  'group' => 'hero', 'label' => 'Hero Microcopy'],
            ['key' => 'hero_bg_from',     'value' => '#7c3aed',  'type' => 'color', 'group' => 'hero', 'label' => 'Hero Gradient From'],
            ['key' => 'hero_bg_to',       'value' => '#4f46e5',  'type' => 'color', 'group' => 'hero', 'label' => 'Hero Gradient To'],
            ['key' => 'hero_image_path',  'value' => '',                                           'type' => 'image', 'group' => 'hero', 'label' => 'Hero Image'],

            // Homepage content
            ['key' => 'utility_badge_text',      'value' => 'Instant Delivery',                        'type' => 'text', 'group' => 'homepage', 'label' => 'Utility Badge'],
            ['key' => 'utility_left_text',       'value' => 'Secure Payments',                         'type' => 'text', 'group' => 'homepage', 'label' => 'Utility Left'],
            ['key' => 'utility_center_text',     'value' => '24/7 Support',                            'type' => 'text', 'group' => 'homepage', 'label' => 'Utility Center'],
            ['key' => 'home_search_placeholder', 'value' => 'Search products...',                      'type' => 'text', 'group' => 'homepage', 'label' => 'Search Placeholder'],
            ['key' => 'feature_one_text',        'value' => 'Instant Delivery',                        'type' => 'text', 'group' => 'homepage', 'label' => 'Feature One'],
            ['key' => 'feature_two_text',        'value' => 'Secure Payment',                          'type' => 'text', 'group' => 'homepage', 'label' => 'Feature Two'],
            ['key' => 'feature_three_text',      'value' => '24/7 Available',                          'type' => 'text', 'group' => 'homepage', 'label' => 'Feature Three'],
            ['key' => 'feature_four_text',       'value' => 'Happy Customers',                         'type' => 'text', 'group' => 'homepage', 'label' => 'Feature Four'],
            ['key' => 'reviews_section_title',   'value' => 'What Customers Say',                      'type' => 'text', 'group' => 'homepage', 'label' => 'Reviews Title'],
            ['key' => 'reviews_section_subtitle','value' => 'Trusted by customers across Sri Lanka',   'type' => 'text', 'group' => 'homepage', 'label' => 'Reviews Subtitle'],
            ['key' => 'final_cta_title',         'value' => 'Do not miss out — grab yours now!',       'type' => 'text', 'group' => 'homepage', 'label' => 'Final CTA Title'],
            ['key' => 'final_cta_subtitle',      'value' => 'Instant delivery. Best prices in Sri Lanka.', 'type' => 'text', 'group' => 'homepage', 'label' => 'Final CTA Subtitle'],
            ['key' => 'final_cta_button_text',   'value' => 'Browse Store',                            'type' => 'text', 'group' => 'homepage', 'label' => 'Final CTA Button Text'],
            ['key' => 'final_cta_button_link',   'value' => '/products',                               'type' => 'text', 'group' => 'homepage', 'label' => 'Final CTA Button Link'],

            // ── Nav ───────────────────────────────────────────────────
            ['key' => 'show_deals_link',        'value' => '1', 'type' => 'boolean', 'group' => 'appearance', 'label' => 'Show Deals Link'],
            ['key' => 'show_new_arrivals_link',  'value' => '1', 'type' => 'boolean', 'group' => 'appearance', 'label' => 'Show New Arrivals Link'],

            // ── Footer ────────────────────────────────────────────────
            ['key' => 'footer_tagline',   'value' => 'Your one-stop shop for everything trendy.', 'type' => 'text', 'group' => 'footer', 'label' => 'Footer Tagline'],
            ['key' => 'footer_copyright', 'value' => '© 2024 DISPLAY LANKA.LK. All rights reserved.', 'type' => 'text', 'group' => 'footer', 'label' => 'Footer Copyright'],
            ['key' => 'facebook_url',     'value' => '#', 'type' => 'text', 'group' => 'social', 'label' => 'Facebook URL'],
            ['key' => 'twitter_url',      'value' => '#', 'type' => 'text', 'group' => 'social', 'label' => 'Twitter URL'],
            ['key' => 'instagram_url',    'value' => '#', 'type' => 'text', 'group' => 'social', 'label' => 'Instagram URL'],
            ['key' => 'pinterest_url',    'value' => '#', 'type' => 'text', 'group' => 'social', 'label' => 'Pinterest URL'],

            // ── Display Items ─────────────────────────────────────────
            ['key' => 'featured_product_ids',      'value' => '[]', 'type' => 'json', 'group' => 'display', 'label' => 'Featured Product IDs'],
            ['key' => 'new_arrivals_ids',           'value' => '[]', 'type' => 'json', 'group' => 'display', 'label' => 'New Arrivals Product IDs'],
            ['key' => 'deal_product_ids',           'value' => '[]', 'type' => 'json', 'group' => 'display', 'label' => 'Deal Product IDs'],
            ['key' => 'featured_section_title',     'value' => 'Featured Products', 'type' => 'text', 'group' => 'display', 'label' => 'Featured Section Title'],
            ['key' => 'new_arrivals_section_title', 'value' => 'New Arrivals',      'type' => 'text', 'group' => 'display', 'label' => 'New Arrivals Section Title'],
            ['key' => 'deals_section_title',        'value' => 'Best Deals',        'type' => 'text', 'group' => 'display', 'label' => 'Deals Section Title'],

            // ── Category Icons ────────────────────────────────────────
            ['key' => 'category_icons', 'value' => '{}', 'type' => 'json', 'group' => 'appearance', 'label' => 'Category Icons'],
        ];

        foreach ($defaults as $setting) {
            SiteSetting::updateOrCreate(
                ['tenant_id' => $tenant->id, 'key' => $setting['key']],
                array_merge($setting, ['tenant_id' => $tenant->id])
            );
        }

        $this->command->info('Site settings seeded successfully.');
    }
}

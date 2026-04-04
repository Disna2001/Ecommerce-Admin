<?php

namespace App\Livewire\Admin\SiteManagement;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Storage;

class AppearanceManager extends Component
{
    use WithFileUploads;

    // ── Branding ─────────────────────────────────────────────
    public $site_name        = 'DISPLAY LANKA.LK';
    public $site_tagline     = 'Your one-stop shop for everything';
    public $logo_image;
    public $favicon_image;
    public $logo_path        = '';
    public $favicon_path     = '';

    // ── Color Theme ──────────────────────────────────────────
    public $primary_color    = '#4f46e5';
    public $secondary_color  = '#7c3aed';
    public $accent_color     = '#06b6d4';
    public $text_color       = '#111827';
    public $bg_color         = '#f9fafb';
    public $nav_bg_color     = '#ffffff';

    // ── Hero Section ─────────────────────────────────────────
    public $hero_title       = 'Summer Sale';
    public $hero_subtitle    = 'Get up to 50% off on trending items';
    public $hero_button_text = 'Shop Now';
    public $hero_button_link = '#';
    public $hero_highlight_text = 'Unbeatable Prices';
    public $hero_microcopy   = 'Delivered to your inbox in seconds.';
    public $hero_bg_from     = '#7c3aed';
    public $hero_bg_to       = '#4f46e5';
    public $hero_image;
    public $hero_image_path  = '';

    // Homepage content
    public $utility_badge_text = 'Instant Delivery';
    public $utility_left_text = 'Secure Payments';
    public $utility_center_text = '24/7 Support';
    public $home_search_placeholder = 'Search products...';
    public $feature_one_text = 'Instant Delivery';
    public $feature_two_text = 'Secure Payment';
    public $feature_three_text = '24/7 Available';
    public $feature_four_text = 'Happy Customers';
    public $reviews_section_title = 'What Customers Say';
    public $reviews_section_subtitle = 'Trusted by customers across Sri Lanka';
    public $final_cta_title = 'Do not miss out — grab yours now!';
    public $final_cta_subtitle = 'Instant delivery. Best prices in Sri Lanka.';
    public $final_cta_button_text = 'Browse Store';
    public $final_cta_button_link = '/products';

    // ── Top Bar ───────────────────────────────────────────────
    public $topbar_text      = 'Summer Sale! Up to 50% off on selected items. Shop Now!';
    public $topbar_enabled   = true;
    public $topbar_bg_from   = '#7c3aed';
    public $topbar_bg_to     = '#4f46e5';

    // ── Category Icons ────────────────────────────────────────
    public $category_icons   = [];

    // ── Footer ────────────────────────────────────────────────
    public $footer_tagline   = 'Your one-stop shop for everything trendy and affordable.';
    public $footer_copyright = '© 2024 ShopEase. All rights reserved.';
    public $facebook_url     = '#';
    public $twitter_url      = '#';
    public $instagram_url    = '#';
    public $pinterest_url    = '#';

    // Payment methods
    public $enable_cod = true;
    public $enable_bank_transfer = true;
    public $enable_card_payment = true;
    public $cod_label = 'Cash on Delivery';
    public $cod_description = 'Pay when your order arrives';
    public $bank_label = 'Bank Transfer';
    public $bank_description = 'Upload your transfer slip for admin verification';
    public $bank_instruction_title = 'Bank transfer verification';
    public $bank_instruction_body = 'Complete your transfer, then upload the payment slip with the bank reference so our team can verify it quickly.';
    public $bank_account_name = '';
    public $bank_account_number = '';
    public $bank_name = '';
    public $bank_branch = '';
    public $card_label = 'Online / Card Payment';
    public $card_description = 'Submit your transaction proof for approval';
    public $card_instruction_title = 'Online payment verification';
    public $card_instruction_body = 'Complete your online or card payment outside the site, then upload the confirmation screenshot or receipt for review.';

    // ── Nav Links ─────────────────────────────────────────────
    public $show_deals_link       = true;
    public $show_new_arrivals_link = true;

    public $activeTab = 'branding';
    public $saved     = false;

    // All FA icon options for categories
    public $iconOptions = [
        'fa-mobile-alt' => 'Mobile / Electronics',
        'fa-tshirt'     => 'Fashion',
        'fa-home'       => 'Home & Living',
        'fa-spa'        => 'Beauty',
        'fa-futbol'     => 'Sports',
        'fa-book'       => 'Books',
        'fa-laptop'     => 'Laptop',
        'fa-tv'         => 'TV',
        'fa-camera'     => 'Camera',
        'fa-headphones' => 'Headphones',
        'fa-car'        => 'Automotive',
        'fa-baby'       => 'Baby',
        'fa-gamepad'    => 'Gaming',
        'fa-dumbbell'   => 'Fitness',
        'fa-utensils'   => 'Kitchen',
        'fa-tools'      => 'Tools',
        'fa-gem'        => 'Jewelry',
        'fa-paw'        => 'Pets',
    ];

    public function mount()
    {
        $this->loadSettings();
    }

    public function updated($property): void
    {
        if (str_ends_with((string) $property, '_image') || str_contains((string) $property, 'site_') || str_starts_with((string) $property, 'hero_') || str_starts_with((string) $property, 'topbar_')) {
            $this->saved = false;
        }
    }

    public function loadSettings()
    {
        $settings = [
            'site_name', 'site_tagline', 'logo_path', 'favicon_path',
            'primary_color', 'secondary_color', 'accent_color', 'text_color', 'bg_color', 'nav_bg_color',
            'hero_title', 'hero_subtitle', 'hero_button_text', 'hero_button_link',
            'hero_highlight_text', 'hero_microcopy', 'hero_bg_from', 'hero_bg_to', 'hero_image_path',
            'utility_badge_text', 'utility_left_text', 'utility_center_text', 'home_search_placeholder',
            'feature_one_text', 'feature_two_text', 'feature_three_text', 'feature_four_text',
            'reviews_section_title', 'reviews_section_subtitle',
            'final_cta_title', 'final_cta_subtitle', 'final_cta_button_text', 'final_cta_button_link',
            'topbar_text', 'topbar_enabled', 'topbar_bg_from', 'topbar_bg_to',
            'footer_tagline', 'footer_copyright',
            'facebook_url', 'twitter_url', 'instagram_url', 'pinterest_url',
            'enable_cod', 'enable_bank_transfer', 'enable_card_payment',
            'cod_label', 'cod_description',
            'bank_label', 'bank_description', 'bank_instruction_title', 'bank_instruction_body',
            'bank_account_name', 'bank_account_number', 'bank_name', 'bank_branch',
            'card_label', 'card_description', 'card_instruction_title', 'card_instruction_body',
            'show_deals_link', 'show_new_arrivals_link',
        ];

        foreach ($settings as $key) {
            $val = SiteSetting::get($key);
            if (!is_null($val)) {
                $this->$key = $val;
            }
        }

        // Load category icons JSON
        $iconsJson = SiteSetting::get('category_icons');
        if ($iconsJson) {
            $this->category_icons = is_array($iconsJson) ? $iconsJson : json_decode($iconsJson, true) ?? [];
        }
    }

    public function saveAll()
    {
        $this->validate([
            'site_name'       => 'required|string|max:100',
            'primary_color'   => 'required|string',
            'secondary_color' => 'required|string',
            'hero_title'      => 'required|string|max:200',
            'logo_image'      => 'nullable|file|mimes:png,jpg,jpeg,webp,svg|max:5120',
            'favicon_image'   => 'nullable|file|mimes:png,jpg,jpeg,webp,svg,ico|max:2048',
            'hero_image'      => 'nullable|image|max:5120',
        ]);

        $textSettings = [
            'site_name', 'site_tagline',
            'primary_color', 'secondary_color', 'accent_color', 'text_color', 'bg_color', 'nav_bg_color',
            'hero_title', 'hero_subtitle', 'hero_button_text', 'hero_button_link', 'hero_highlight_text', 'hero_microcopy',
            'hero_bg_from', 'hero_bg_to',
            'utility_badge_text', 'utility_left_text', 'utility_center_text', 'home_search_placeholder',
            'feature_one_text', 'feature_two_text', 'feature_three_text', 'feature_four_text',
            'reviews_section_title', 'reviews_section_subtitle',
            'final_cta_title', 'final_cta_subtitle', 'final_cta_button_text', 'final_cta_button_link',
            'topbar_text', 'topbar_bg_from', 'topbar_bg_to',
            'footer_tagline', 'footer_copyright',
            'facebook_url', 'twitter_url', 'instagram_url', 'pinterest_url',
            'cod_label', 'cod_description',
            'bank_label', 'bank_description', 'bank_instruction_title', 'bank_instruction_body',
            'bank_account_name', 'bank_account_number', 'bank_name', 'bank_branch',
            'card_label', 'card_description', 'card_instruction_title', 'card_instruction_body',
        ];

        foreach ($textSettings as $key) {
            SiteSetting::set($key, $this->$key, 'text', $this->groupFor($key));
        }

        // Boolean settings
        SiteSetting::set('topbar_enabled',        $this->topbar_enabled        ? '1' : '0', 'boolean', 'appearance');
        SiteSetting::set('show_deals_link',        $this->show_deals_link        ? '1' : '0', 'boolean', 'appearance');
        SiteSetting::set('show_new_arrivals_link', $this->show_new_arrivals_link ? '1' : '0', 'boolean', 'appearance');
        SiteSetting::set('enable_cod', $this->enable_cod ? '1' : '0', 'boolean', 'payment');
        SiteSetting::set('enable_bank_transfer', $this->enable_bank_transfer ? '1' : '0', 'boolean', 'payment');
        SiteSetting::set('enable_card_payment', $this->enable_card_payment ? '1' : '0', 'boolean', 'payment');

        // Category icons JSON
        SiteSetting::set('category_icons', json_encode($this->category_icons), 'json', 'appearance');

        $this->persistUploadedAsset('logo_image', 'logo_path', 'site', 'branding');
        $this->persistUploadedAsset('favicon_image', 'favicon_path', 'site', 'branding');
        $this->persistUploadedAsset('hero_image', 'hero_image_path', 'site/hero', 'hero');

        $this->saved = true;
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Appearance settings saved!']);
    }

    public function removeLogo()
    {
        $this->removeAsset('logo_path', 'branding', 'Site logo removed.');
        $this->logo_image = null;
    }

    public function removeFavicon()
    {
        $this->removeAsset('favicon_path', 'branding', 'Favicon removed.');
        $this->favicon_image = null;
    }

    public function removeHeroImage()
    {
        $this->removeAsset('hero_image_path', 'hero', 'Hero image removed.');
        $this->hero_image = null;
    }

    private function removeAsset(string $property, string $group, string $message): void
    {
        $path = $this->$property;

        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }

        SiteSetting::set($property, '', 'image', $group);
        $this->$property = '';
        $this->saved = false;

        $this->dispatch('notify', ['type' => 'success', 'message' => $message]);
    }

    private function persistUploadedAsset(string $uploadProperty, string $settingKey, string $directory, string $group): void
    {
        if (!$this->$uploadProperty) {
            return;
        }

        $oldPath = $this->$settingKey;
        $newPath = $this->$uploadProperty->store($directory, 'public');

        if ($oldPath && $oldPath !== $newPath && Storage::disk('public')->exists($oldPath)) {
            Storage::disk('public')->delete($oldPath);
        }

        SiteSetting::set($settingKey, $newPath, 'image', $group);
        $this->$settingKey = $newPath;
        $this->$uploadProperty = null;
    }

    private function groupFor(string $key): string
    {
        if (str_starts_with($key, 'hero_'))     return 'hero';
        if (str_starts_with($key, 'utility_'))  return 'homepage';
        if (str_starts_with($key, 'feature_'))  return 'homepage';
        if (str_starts_with($key, 'reviews_'))  return 'homepage';
        if (str_starts_with($key, 'final_cta_')) return 'homepage';
        if ($key === 'home_search_placeholder') return 'homepage';
        if (str_starts_with($key, 'topbar_'))   return 'appearance';
        if (str_starts_with($key, 'footer_'))   return 'footer';
        if (str_starts_with($key, 'bank_'))     return 'payment';
        if (str_starts_with($key, 'card_'))     return 'payment';
        if (str_starts_with($key, 'cod_'))      return 'payment';
        if (str_starts_with($key, 'enable_'))   return 'payment';
        if (str_starts_with($key, 'site_'))     return 'branding';
        if (str_contains($key, '_color'))       return 'appearance';
        if (in_array($key, ['facebook_url', 'twitter_url', 'instagram_url', 'pinterest_url'])) return 'social';
        return 'general';
    }

    public function render()
    {
        $featuredIds = SiteSetting::get('featured_product_ids', []);

        return view('livewire.admin.site-management.appearance-manager', [
            'storefrontSummary' => [
                'branding_ready' => filled($this->site_name) && filled($this->logo_path) && filled($this->favicon_path),
                'hero_ready' => filled($this->hero_title) && filled($this->hero_button_text),
                'payments_enabled' => collect([$this->enable_cod, $this->enable_bank_transfer, $this->enable_card_payment])->filter()->count(),
                'featured_items' => is_array($featuredIds) ? count($featuredIds) : 0,
            ],
            'tabStats' => [
                'branding' => filled($this->logo_path) ? 'Logo ready' : 'Logo missing',
                'homepage' => filled($this->final_cta_title) ? 'CTA set' : 'CTA missing',
                'colors' => $this->primary_color,
                'hero' => filled($this->hero_image_path) ? 'Image ready' : 'No hero image',
                'topbar' => $this->topbar_enabled ? 'Enabled' : 'Hidden',
                'payment' => collect([$this->enable_cod, $this->enable_bank_transfer, $this->enable_card_payment])->filter()->count() . ' active',
                'categories' => count($this->category_icons) . ' mapped',
                'footer' => filled($this->footer_tagline) ? 'Footer ready' : 'Needs copy',
            ],
        ]);
    }
}

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
    public $hero_layout      = 'split';
    public $hero_alignment   = 'left';
    public $hero_surface     = 'soft';
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
    public $featured_section_title = 'Featured';
    public $new_arrivals_section_title = 'New Arrivals';
    public $deals_section_title = 'Best Sellers';
    public $reviews_section_title = 'What Customers Say';
    public $reviews_section_subtitle = 'Trusted by customers across Sri Lanka';
    public $final_cta_title = 'Do not miss out — grab yours now!';
    public $final_cta_subtitle = 'Instant delivery. Best prices in Sri Lanka.';
    public $final_cta_button_text = 'Browse Store';
    public $final_cta_button_link = '/products';
    public $promo_strip_enabled = true;
    public $promo_strip_badge = 'Limited Drop';
    public $promo_strip_title = 'Build a stronger campaign story under the hero';
    public $promo_strip_text = 'Highlight free delivery, weekend discounts, or bundle offers in one strong promotional rail.';
    public $promo_strip_button_text = 'Explore deals';
    public $promo_strip_button_link = '/products';
    public $promo_strip_from = '#0f172a';
    public $promo_strip_to = '#334155';

    // ── Top Bar ───────────────────────────────────────────────
    public $topbar_text      = 'Summer Sale! Up to 50% off on selected items. Shop Now!';
    public $topbar_enabled   = true;
    public $topbar_bg_from   = '#7c3aed';
    public $topbar_bg_to     = '#4f46e5';

    // ── Category Icons ────────────────────────────────────────
    public $category_icons   = [];
    public $category_strip_title = 'Shop by category';
    public $category_strip_subtitle = 'Jump straight into the product family you need.';
    public $category_strip_style = 'chips';
    public $category_strip_limit = 8;
    public $category_show_icons = true;
    public $catalog_hero_badge = 'Browse Store';
    public $catalog_hero_title = 'Find the right product faster.';
    public $catalog_hero_subtitle = 'Explore premium digital products, filter by category or brand, and sort results the way you want.';
    public $detail_trust_one_title = 'Fast handling';
    public $detail_trust_one_text = 'Orders are processed quickly with status updates sent to your email.';
    public $detail_trust_two_title = 'Payment confidence';
    public $detail_trust_two_text = 'Bank and online payments are verified, and checkout keeps a full order trail.';
    public $detail_trust_three_title = 'Support ready';
    public $detail_trust_three_text = 'You can track each step after ordering and get clear updates if verification is needed.';
    public $detail_value_title = 'Why shoppers choose this listing';
    public $detail_value_text = 'Clear pricing, direct checkout, and order notifications at each major status change.';
    public $detail_value_cta = 'Checkout ready';
    public $detail_in_stock_label = 'In Stock';
    public $detail_low_stock_template = 'Only {quantity} left!';
    public $detail_out_of_stock_label = 'Out of Stock';
    public $detail_related_title = 'Related Products';
    public $detail_show_reviews = true;
    public $detail_show_related = true;

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
    public $enable_payhere_gateway = false;
    public $payhere_label = 'PayHere Gateway';
    public $payhere_description = 'Pay online with cards and supported Sri Lankan payment apps.';
    public $payhere_instruction_title = 'Pay online securely';
    public $payhere_instruction_body = 'You will be redirected to PayHere to complete the payment securely.';
    public $payhere_merchant_id = '';
    public $payhere_merchant_secret = '';
    public $payhere_sandbox = true;
    public $app_public_url = '';
    public $enable_google_login = false;
    public $google_client_id = '';
    public $google_client_secret = '';
    public $google_redirect_uri = '';
    public $enable_facebook_login = false;
    public $facebook_client_id = '';
    public $facebook_client_secret = '';
    public $facebook_redirect_uri = '';

    // ── Nav Links ─────────────────────────────────────────────
    public $show_deals_link       = true;
    public $show_new_arrivals_link = true;
    public $nav_products_label    = 'Products';
    public $nav_categories_label  = 'Categories';
    public $nav_deals_label       = 'Deals';
    public $nav_reviews_label     = 'Reviews';
    public $nav_track_label       = 'Track';
    public $nav_help_label        = 'Help';
    public $support_email         = '';
    public $support_phone         = '';
    public $support_whatsapp      = '';
    public $support_hours         = 'Open daily | Fast support responses';

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
        if (
            str_ends_with((string) $property, '_image') ||
            str_contains((string) $property, 'site_') ||
            str_starts_with((string) $property, 'hero_') ||
            str_starts_with((string) $property, 'utility_') ||
            str_starts_with((string) $property, 'feature_') ||
            str_starts_with((string) $property, 'featured_') ||
            str_starts_with((string) $property, 'new_arrivals_') ||
            str_starts_with((string) $property, 'deals_') ||
            str_starts_with((string) $property, 'reviews_') ||
            str_starts_with((string) $property, 'final_cta_') ||
            str_starts_with((string) $property, 'topbar_') ||
            str_starts_with((string) $property, 'promo_strip_') ||
            str_starts_with((string) $property, 'category_strip_') ||
            str_starts_with((string) $property, 'catalog_hero_') ||
            str_starts_with((string) $property, 'detail_') ||
            str_starts_with((string) $property, 'cod_') ||
            str_starts_with((string) $property, 'bank_') ||
            str_starts_with((string) $property, 'card_') ||
            str_starts_with((string) $property, 'payhere_') ||
            str_starts_with((string) $property, 'google_') ||
            str_starts_with((string) $property, 'facebook_') ||
            str_starts_with((string) $property, 'nav_') ||
            str_starts_with((string) $property, 'support_') ||
            $property === 'app_public_url' ||
            $property === 'category_show_icons' ||
            in_array($property, ['enable_cod', 'enable_bank_transfer', 'enable_card_payment', 'enable_payhere_gateway', 'enable_google_login', 'enable_facebook_login'], true)
        ) {
            $this->saved = false;
        }
    }

    public function loadSettings()
    {
        $settings = [
            'site_name', 'site_tagline', 'logo_path', 'favicon_path',
            'primary_color', 'secondary_color', 'accent_color', 'text_color', 'bg_color', 'nav_bg_color',
            'hero_title', 'hero_subtitle', 'hero_button_text', 'hero_button_link',
            'hero_highlight_text', 'hero_microcopy', 'hero_bg_from', 'hero_bg_to', 'hero_layout', 'hero_alignment', 'hero_surface', 'hero_image_path',
            'utility_badge_text', 'utility_left_text', 'utility_center_text', 'home_search_placeholder',
            'feature_one_text', 'feature_two_text', 'feature_three_text', 'feature_four_text',
            'featured_section_title', 'new_arrivals_section_title', 'deals_section_title',
            'reviews_section_title', 'reviews_section_subtitle',
            'final_cta_title', 'final_cta_subtitle', 'final_cta_button_text', 'final_cta_button_link',
            'promo_strip_enabled', 'promo_strip_badge', 'promo_strip_title', 'promo_strip_text',
            'promo_strip_button_text', 'promo_strip_button_link', 'promo_strip_from', 'promo_strip_to',
            'topbar_text', 'topbar_enabled', 'topbar_bg_from', 'topbar_bg_to',
            'category_strip_title', 'category_strip_subtitle', 'category_strip_style', 'category_strip_limit', 'category_show_icons',
            'catalog_hero_badge', 'catalog_hero_title', 'catalog_hero_subtitle',
            'detail_trust_one_title', 'detail_trust_one_text', 'detail_trust_two_title', 'detail_trust_two_text',
            'detail_trust_three_title', 'detail_trust_three_text', 'detail_value_title', 'detail_value_text',
            'detail_value_cta', 'detail_in_stock_label', 'detail_low_stock_template', 'detail_out_of_stock_label',
            'detail_related_title', 'detail_show_reviews', 'detail_show_related',
            'footer_tagline', 'footer_copyright',
            'facebook_url', 'twitter_url', 'instagram_url', 'pinterest_url',
            'enable_cod', 'enable_bank_transfer', 'enable_card_payment',
            'cod_label', 'cod_description',
            'bank_label', 'bank_description', 'bank_instruction_title', 'bank_instruction_body',
            'bank_account_name', 'bank_account_number', 'bank_name', 'bank_branch',
            'card_label', 'card_description', 'card_instruction_title', 'card_instruction_body',
            'enable_payhere_gateway', 'payhere_label', 'payhere_description', 'payhere_instruction_title',
            'payhere_instruction_body', 'payhere_merchant_id', 'payhere_merchant_secret', 'payhere_sandbox',
            'app_public_url',
            'enable_google_login', 'google_client_id', 'google_client_secret', 'google_redirect_uri',
            'enable_facebook_login', 'facebook_client_id', 'facebook_client_secret', 'facebook_redirect_uri',
            'show_deals_link', 'show_new_arrivals_link',
            'nav_products_label', 'nav_categories_label', 'nav_deals_label', 'nav_reviews_label', 'nav_track_label', 'nav_help_label',
            'support_email', 'support_phone', 'support_whatsapp', 'support_hours',
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
            'support_email'   => 'nullable|email|max:120',
            'category_strip_limit' => 'nullable|integer|min:4|max:12',
            'app_public_url' => 'nullable|url|max:255',
            'google_redirect_uri' => 'nullable|url|max:255',
            'facebook_redirect_uri' => 'nullable|url|max:255',
        ]);

        $textSettings = [
            'site_name', 'site_tagline',
            'primary_color', 'secondary_color', 'accent_color', 'text_color', 'bg_color', 'nav_bg_color',
            'hero_title', 'hero_subtitle', 'hero_button_text', 'hero_button_link', 'hero_highlight_text', 'hero_microcopy',
            'hero_bg_from', 'hero_bg_to', 'hero_layout', 'hero_alignment', 'hero_surface',
            'utility_badge_text', 'utility_left_text', 'utility_center_text', 'home_search_placeholder',
            'feature_one_text', 'feature_two_text', 'feature_three_text', 'feature_four_text',
            'featured_section_title', 'new_arrivals_section_title', 'deals_section_title',
            'reviews_section_title', 'reviews_section_subtitle',
            'final_cta_title', 'final_cta_subtitle', 'final_cta_button_text', 'final_cta_button_link',
            'promo_strip_badge', 'promo_strip_title', 'promo_strip_text', 'promo_strip_button_text',
            'promo_strip_button_link', 'promo_strip_from', 'promo_strip_to',
            'topbar_text', 'topbar_bg_from', 'topbar_bg_to',
            'category_strip_title', 'category_strip_subtitle', 'category_strip_style', 'catalog_hero_badge', 'catalog_hero_title', 'catalog_hero_subtitle',
            'detail_trust_one_title', 'detail_trust_one_text', 'detail_trust_two_title', 'detail_trust_two_text',
            'detail_trust_three_title', 'detail_trust_three_text', 'detail_value_title', 'detail_value_text',
            'detail_value_cta', 'detail_in_stock_label', 'detail_low_stock_template', 'detail_out_of_stock_label',
            'detail_related_title',
            'footer_tagline', 'footer_copyright',
            'facebook_url', 'twitter_url', 'instagram_url', 'pinterest_url',
            'cod_label', 'cod_description',
            'bank_label', 'bank_description', 'bank_instruction_title', 'bank_instruction_body',
            'bank_account_name', 'bank_account_number', 'bank_name', 'bank_branch',
            'card_label', 'card_description', 'card_instruction_title', 'card_instruction_body',
            'payhere_label', 'payhere_description', 'payhere_instruction_title', 'payhere_instruction_body',
            'payhere_merchant_id', 'payhere_merchant_secret',
            'app_public_url',
            'google_client_id', 'google_client_secret', 'google_redirect_uri',
            'facebook_client_id', 'facebook_client_secret', 'facebook_redirect_uri',
            'nav_products_label', 'nav_categories_label', 'nav_deals_label', 'nav_reviews_label', 'nav_track_label', 'nav_help_label',
            'support_email', 'support_phone', 'support_whatsapp', 'support_hours',
        ];

        foreach ($textSettings as $key) {
            SiteSetting::set($key, $this->$key, 'text', $this->groupFor($key));
        }

        // Boolean settings
        SiteSetting::set('topbar_enabled',        $this->topbar_enabled        ? '1' : '0', 'boolean', 'appearance');
        SiteSetting::set('promo_strip_enabled',   $this->promo_strip_enabled   ? '1' : '0', 'boolean', 'homepage');
        SiteSetting::set('show_deals_link',        $this->show_deals_link        ? '1' : '0', 'boolean', 'appearance');
        SiteSetting::set('show_new_arrivals_link', $this->show_new_arrivals_link ? '1' : '0', 'boolean', 'appearance');
        SiteSetting::set('category_show_icons',    $this->category_show_icons    ? '1' : '0', 'boolean', 'appearance');
        SiteSetting::set('detail_show_reviews',    $this->detail_show_reviews    ? '1' : '0', 'boolean', 'appearance');
        SiteSetting::set('detail_show_related',    $this->detail_show_related    ? '1' : '0', 'boolean', 'appearance');
        SiteSetting::set('enable_cod', $this->enable_cod ? '1' : '0', 'boolean', 'payment');
        SiteSetting::set('enable_bank_transfer', $this->enable_bank_transfer ? '1' : '0', 'boolean', 'payment');
        SiteSetting::set('enable_card_payment', $this->enable_card_payment ? '1' : '0', 'boolean', 'payment');
        SiteSetting::set('enable_payhere_gateway', $this->enable_payhere_gateway ? '1' : '0', 'boolean', 'payment');
        SiteSetting::set('payhere_sandbox', $this->payhere_sandbox ? '1' : '0', 'boolean', 'payment');
        SiteSetting::set('enable_google_login', $this->enable_google_login ? '1' : '0', 'boolean', 'integrations');
        SiteSetting::set('enable_facebook_login', $this->enable_facebook_login ? '1' : '0', 'boolean', 'integrations');

        // Category icons JSON
        SiteSetting::set('category_icons', json_encode($this->category_icons), 'json', 'appearance');
        SiteSetting::set('category_strip_limit', max(4, min(12, (int) $this->category_strip_limit)), 'text', 'appearance');

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
        if (str_starts_with($key, 'featured_')) return 'homepage';
        if (str_starts_with($key, 'new_arrivals_')) return 'homepage';
        if (str_starts_with($key, 'deals_')) return 'homepage';
        if (str_starts_with($key, 'reviews_'))  return 'homepage';
        if (str_starts_with($key, 'final_cta_')) return 'homepage';
        if (str_starts_with($key, 'promo_strip_')) return 'homepage';
        if ($key === 'home_search_placeholder') return 'homepage';
        if (str_starts_with($key, 'category_strip_')) return 'appearance';
        if (str_starts_with($key, 'catalog_hero_')) return 'appearance';
        if (str_starts_with($key, 'detail_')) return 'appearance';
        if (str_starts_with($key, 'topbar_'))   return 'appearance';
        if (str_starts_with($key, 'footer_'))   return 'footer';
        if (str_starts_with($key, 'bank_'))     return 'payment';
        if (str_starts_with($key, 'card_'))     return 'payment';
        if (str_starts_with($key, 'payhere_'))  return 'payment';
        if (str_starts_with($key, 'cod_'))      return 'payment';
        if (str_starts_with($key, 'google_'))   return 'integrations';
        if (str_starts_with($key, 'facebook_')) return 'integrations';
        if ($key === 'app_public_url')          return 'hosting';
        if (str_starts_with($key, 'enable_'))   return 'payment';
        if (str_starts_with($key, 'site_'))     return 'branding';
        if (str_starts_with($key, 'nav_'))      return 'appearance';
        if (str_starts_with($key, 'support_'))  return 'footer';
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
                'payments_enabled' => collect([$this->enable_cod, $this->enable_bank_transfer, $this->enable_card_payment, $this->enable_payhere_gateway])->filter()->count(),
                'integrations_ready' => collect([
                    $this->enable_payhere_gateway && filled($this->payhere_merchant_id) && filled($this->payhere_merchant_secret),
                    $this->enable_google_login && filled($this->google_client_id) && filled($this->google_client_secret),
                    $this->enable_facebook_login && filled($this->facebook_client_id) && filled($this->facebook_client_secret),
                ])->filter()->count(),
                'featured_items' => is_array($featuredIds) ? count($featuredIds) : 0,
            ],
            'tabStats' => [
                'branding' => filled($this->logo_path) ? 'Logo ready' : 'Logo missing',
                'homepage' => filled($this->final_cta_title) ? 'CTA set' : 'CTA missing',
                'colors' => $this->primary_color,
                'hero' => filled($this->hero_image_path) ? ucfirst($this->hero_layout) . ' layout' : 'No hero image',
                'sections' => $this->promo_strip_enabled ? 'Promo live' : 'Promo hidden',
                'topbar' => $this->topbar_enabled ? 'Enabled' : 'Hidden',
                'detail' => $this->detail_show_related ? 'Related on' : 'Minimal detail',
                'payment' => collect([$this->enable_cod, $this->enable_bank_transfer, $this->enable_card_payment, $this->enable_payhere_gateway])->filter()->count() . ' active',
                'integrations' => collect([$this->enable_payhere_gateway, $this->enable_google_login, $this->enable_facebook_login])->filter()->count() . ' active',
                'categories' => count($this->category_icons) . ' mapped',
                'navigation' => ($this->show_deals_link || $this->show_new_arrivals_link) ? 'Dynamic links' : 'Minimal nav',
                'footer' => filled($this->footer_tagline) ? 'Footer ready' : 'Needs copy',
            ],
        ]);
    }
}

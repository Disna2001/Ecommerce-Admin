<?php

namespace App\Services\Storefront;

use App\Models\Category;
use App\Models\Banner;
use App\Models\Review;
use App\Models\SiteSetting;
use App\Models\Stock;
use App\Services\Tenancy\TenantManager;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class StorefrontDataService
{
    public function __construct(
        protected ProductPricingService $productPricingService
    ) {
    }

    public function getSharedLayoutData(): array
    {
        return $this->rememberOrCompute('storefront_shared_layout_data', 600, function () {
            $categoryStripLimit = max(4, min(12, (int) SiteSetting::get('category_strip_limit', 8)));
            return [
                'siteName' => (string) SiteSetting::get('site_name', 'DISPLAY LANKA.LK'),
                'siteTagline' => (string) SiteSetting::get('site_tagline', 'Your one-stop shop'),
                'logoPath' => (string) (SiteSetting::get('logo_path') ?: ''),
                'faviconPath' => (string) (SiteSetting::get('favicon_path') ?: ''),
                'primaryColor' => (string) SiteSetting::get('primary_color', '#6d28d9'),
                'secondaryColor' => (string) SiteSetting::get('secondary_color', '#7c3aed'),
                'accentColor' => (string) SiteSetting::get('accent_color', '#06b6d4'),
                'textColor' => (string) SiteSetting::get('text_color', '#111827'),
                'bgColor' => (string) SiteSetting::get('bg_color', '#f8fafc'),
                'navBgColor' => (string) SiteSetting::get('nav_bg_color', '#ffffff'),
                'assetCdnUrl' => SiteSetting::get('asset_cdn_url', ''),
                'topbarEnabled' => SiteSetting::get('topbar_enabled', true),
                'topbarText' => SiteSetting::get('topbar_text', 'Fast delivery across Sri Lanka'),
                'topbarFrom' => SiteSetting::get('topbar_bg_from', '#6d28d9'),
                'topbarTo' => SiteSetting::get('topbar_bg_to', '#8b5cf6'),
                'utilityBadge' => SiteSetting::get('utility_badge_text', 'Instant Delivery'),
                'utilityLeft' => SiteSetting::get('utility_left_text', 'Secure Payments'),
                'utilityCenter' => SiteSetting::get('utility_center_text', '24/7 Support'),
                'searchPlaceholder' => SiteSetting::get('home_search_placeholder', 'Search products...'),
                'footerTagline' => SiteSetting::get('footer_tagline', 'Premium digital subscriptions at unbeatable prices.'),
                'footerCopy' => preg_replace('/\b20\d{2}\b/', date('Y'), SiteSetting::get('footer_copyright', '© '.date('Y').' '.SiteSetting::get('site_name', 'DISPLAY LANKA.LK').'. All rights reserved.')),
                'fbUrl' => SiteSetting::get('facebook_url', '#'),
                'twUrl' => SiteSetting::get('twitter_url', '#'),
                'igUrl' => SiteSetting::get('instagram_url', '#'),
                'piUrl' => SiteSetting::get('pinterest_url', '#'),
                'supportEmail' => SiteSetting::get('support_email', ''),
                'supportPhone' => SiteSetting::get('support_phone', ''),
                'supportWhatsapp' => SiteSetting::get('support_whatsapp', ''),
                'supportHours' => SiteSetting::get('support_hours', 'Open daily | Fast support responses'),
                'navProductsLabel' => SiteSetting::get('nav_products_label', 'Products'),
                'navCategoriesLabel' => SiteSetting::get('nav_categories_label', 'Categories'),
                'navDealsLabel' => SiteSetting::get('nav_deals_label', 'Deals'),
                'navReviewsLabel' => SiteSetting::get('nav_reviews_label', 'Reviews'),
                'navTrackLabel' => SiteSetting::get('nav_track_label', 'Track'),
                'navHelpLabel' => SiteSetting::get('nav_help_label', 'Help'),
                'showDealsLink' => SiteSetting::get('show_deals_link', true),
                'showNewArrivalsLink' => SiteSetting::get('show_new_arrivals_link', true),
                'categoryStripTitle' => SiteSetting::get('category_strip_title', 'Shop by category'),
                'categoryStripSubtitle' => SiteSetting::get('category_strip_subtitle', 'Jump straight into the product family you need.'),
                'categoryStripStyle' => SiteSetting::get('category_strip_style', 'chips'),
                'categoryStripLimit' => $categoryStripLimit,
                'categoryShowIcons' => SiteSetting::get('category_show_icons', true),
                'categoryIcons' => SiteSetting::get('category_icons', []),
                'categories' => Category::query()->select('id', 'name')->orderBy('name')->take($categoryStripLimit)->get(),
                // Category strip copy
                'categoryH2' => SiteSetting::get('category_section_h2', 'Shop by Category'),
                'categorySeeAll' => SiteSetting::get('category_see_all_label', 'See All'),
                'categoryAllLabel' => SiteSetting::get('category_all_label', 'Full Catalog'),
                'categoryAllSub' => SiteSetting::get('category_all_sub_label', 'All Products'),
                'categoryClassificationLabel' => SiteSetting::get('category_classification_label', 'Category'),
                // Fonts
                'headingFont' => SiteSetting::get('heading_font', 'Plus Jakarta Sans'),
                'bodyFont' => SiteSetting::get('body_font', 'Figtree'),
                // Footer copy
                'footerColShop' => SiteSetting::get('footer_col_shop_label', 'Shop'),
                'footerColLegal' => SiteSetting::get('footer_col_legal_label', 'Legal'),
                'footerLinkSignin' => SiteSetting::get('footer_link_signin_label', 'Sign In'),
                'footerLinkPrivacy' => SiteSetting::get('footer_link_privacy_label', 'Privacy Policy'),
                'footerLinkRefund' => SiteSetting::get('footer_link_refund_label', 'Refund Policy'),
            ];
        });
    }

    public function getHomePageData(): array
    {
        $shared = $this->getSharedLayoutData();

        $featuredIds = (array) (SiteSetting::get('featured_product_ids', []) ?? []);
        $newIds = (array) (SiteSetting::get('new_arrivals_ids', []) ?? []);
        $dealIds = (array) (SiteSetting::get('deal_product_ids', []) ?? []);

        return array_merge($shared, [
            'heroTitle' => SiteSetting::get('hero_title', 'Genuine Smartphone Displays & Parts'),
            'heroHighlight' => SiteSetting::get('hero_highlight_text', 'Unbeatable Quality'),
            'heroSubtitle' => SiteSetting::get('hero_subtitle', 'Original display assemblies, replacement screens, and electronic components.'),
            'heroMicrocopy' => SiteSetting::get('hero_microcopy', 'Fast Sri Lanka-wide delivery & order tracking.'),
            'heroBgFrom' => SiteSetting::get('hero_bg_from', '#7c3aed'),
            'heroBgTo' => SiteSetting::get('hero_bg_to', '#4f46e5'),
            'heroLayout' => SiteSetting::get('hero_layout', 'split'),
            'heroAlignment' => SiteSetting::get('hero_alignment', 'left'),
            'heroSurface' => SiteSetting::get('hero_surface', 'soft'),
            'heroImagePath' => SiteSetting::get('hero_image_path', ''),
            'heroBtnText' => SiteSetting::get('hero_button_text', 'Shop Collection'),
            'heroBtnLink' => SiteSetting::get('hero_button_link', '/products'),
            'heroSlideshowEnabled' => (bool) SiteSetting::get('hero_slideshow_enabled', true),
            'heroSlideshowAutoplay' => (bool) SiteSetting::get('hero_slideshow_autoplay', true),
            'featureOne' => SiteSetting::get('feature_one_text', 'Fast Delivery'),
            'featureTwo' => SiteSetting::get('feature_two_text', 'Secure Payment'),
            'featureThree' => SiteSetting::get('feature_three_text', '24/7 Available'),
            'featureFour' => SiteSetting::get('feature_four_text', 'Happy Customers'),
            'featuredTitle' => SiteSetting::get('featured_section_title', 'Featured'),
            'featuredSubtitle' => SiteSetting::get('featured_section_subtitle', 'High-conviction picks for the homepage.'),
            'newTitle' => SiteSetting::get('new_arrivals_section_title', 'New Arrivals'),
            'newSubtitle' => SiteSetting::get('new_arrivals_section_subtitle', 'Fresh products customers should notice first.'),
            'dealsTitle' => SiteSetting::get('deals_section_title', 'Best Sellers'),
            'dealsSubtitle' => SiteSetting::get('deals_section_subtitle', 'Price-led products with the strongest promo story.'),
            'railLayout' => SiteSetting::get('rail_layout', 'immersive'),
            'showRailQuantity' => SiteSetting::get('show_rail_quantity', true),
            'showRailStockStatus' => SiteSetting::get('show_rail_stock_status', true),
            'productsPerRail' => (int) SiteSetting::get('products_per_rail', 8),
            'reviewsSectionTitle' => SiteSetting::get('reviews_section_title', 'What Customers Say'),
            'reviewsSectionSubtitle' => SiteSetting::get('reviews_section_subtitle', 'Trusted by customers across Sri Lanka'),
            'finalCtaTitle' => SiteSetting::get('final_cta_title', 'Don\'t miss out — shop the collection now!'),
            'finalCtaSubtitle' => SiteSetting::get('final_cta_subtitle', 'Fast Sri Lanka-wide delivery & genuine quality guarantee.'),
            'finalCtaButtonText' => SiteSetting::get('final_cta_button_text', 'Browse Store'),
            'finalCtaButtonLink' => SiteSetting::get('final_cta_button_link', '/products'),
            'promoStripEnabled' => SiteSetting::get('promo_strip_enabled', true),
            'promoStripBadge' => SiteSetting::get('promo_strip_badge', 'Island-Wide Delivery'),
            'promoStripTitle' => SiteSetting::get('promo_strip_title', 'Quality Smartphone Displays & Parts Across Sri Lanka'),
            'promoStripText' => SiteSetting::get('promo_strip_text', 'Genuine smartphone display assemblies and electronics parts delivered to your doorstep. Track your order in real time.'),
            'promoStripButtonText' => SiteSetting::get('promo_strip_button_text', 'Shop Now'),
            'promoStripButtonLink' => SiteSetting::get('promo_strip_button_link', '/products'),
            // Reviews & CTA eyebrows
            'reviewsEyebrow' => SiteSetting::get('reviews_eyebrow', 'Customer Reviews'),
            'finalCtaEyebrow' => SiteSetting::get('final_cta_eyebrow', 'Shop Now'),
            'promoStripFrom' => SiteSetting::get('promo_strip_from', '#0f172a'),
            'promoStripTo' => SiteSetting::get('promo_strip_to', '#334155'),
            'heroBanners' => Banner::active()->where('position', 'hero')->take(3)->get(),
            'promoBanners' => Banner::active()->where('position', 'promo')->take(3)->get(),
            'featured' => $this->enrichProducts($this->getProductsByIds('home_featured_products_', $featuredIds)),
            'newArrivals' => $this->enrichProducts($this->getProductsByIds('home_new_products_', $newIds)),
            'deals' => $this->enrichProducts($this->getProductsByIds('home_deal_products_', $dealIds)),
            'reviews' => $this->rememberOrCompute('home_latest_reviews', 300, function () {
                return Review::with('user')
                    ->where('is_approved', true)
                    ->latest()
                    ->take(3)
                    ->get();
            }),
            'personalizedRecommendations' => $this->enrichProducts(
                app(CustomerPreferenceService::class)->getRecommendedProducts(auth()->user(), 4)
            ),
        ]);
    }

    public function enrichForStorefrontCards(Collection $products): Collection
    {
        return $this->enrichProducts($products);
    }

    protected function getProductsByIds(string $prefix, array $ids): Collection
    {
        return $this->rememberOrCompute($prefix.md5(json_encode($ids)), 600, function () use ($ids) {
            return !empty($ids)
                ? Stock::with(['brand', 'category'])
                    ->visibleOnStorefront()
                    ->whereIn('id', $ids)
                    ->get()
                : collect();
        });
    }

    protected function rememberOrCompute(string $key, int $seconds, callable $callback): mixed
    {
        $userSuffix = '';
        if (auth()->check() && auth()->user()->hasRole('Merchant')) {
            $userSuffix = '_merchant';
        }
        $scopedKey = app(TenantManager::class)->scopedCacheKey($key . $userSuffix);

        try {
            return Cache::remember($scopedKey, $seconds, $callback);
        } catch (\Throwable) {
            return $callback();
        }
    }

    protected function enrichProducts(Collection $products): Collection
    {
        return $products->map(function (Stock $product) {
            $product->setAttribute('final_price', $this->productPricingService->finalPriceForProduct($product));
            $product->setAttribute('primary_image_url', $this->productPricingService->imageUrlForProduct($product, 'card'));
            $product->setAttribute('primary_image_sources', $this->productPricingService->imageSourcesForProduct($product, 'card'));
            
            if (auth()->check() && auth()->user()->hasRole('Merchant') && filled($product->wholesale_price) && (float)$product->wholesale_price > 0) {
                $product->setAttribute('discount_badge', 'Wholesale');
            } else {
                $discount = $this->productPricingService->resolveDiscountForProduct($product);
                $product->setAttribute(
                    'discount_badge',
                    $discount
                        ? ($discount->type === 'percentage'
                            ? '-'.$discount->value.'%'
                            : '-Rs '.number_format((float) $discount->value, 0))
                        : null
                );
            }

            return $product;
        });
    }
}

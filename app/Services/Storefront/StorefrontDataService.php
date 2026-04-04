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
                'siteName' => SiteSetting::get('site_name', 'DISPLAY LANKA.LK'),
                'siteTagline' => SiteSetting::get('site_tagline', 'Your one-stop shop'),
                'logoPath' => SiteSetting::get('logo_path', ''),
                'faviconPath' => SiteSetting::get('favicon_path', ''),
                'primaryColor' => SiteSetting::get('primary_color', '#6d28d9'),
                'secondaryColor' => SiteSetting::get('secondary_color', '#7c3aed'),
                'accentColor' => SiteSetting::get('accent_color', '#06b6d4'),
                'textColor' => SiteSetting::get('text_color', '#111827'),
                'bgColor' => SiteSetting::get('bg_color', '#f8fafc'),
                'navBgColor' => SiteSetting::get('nav_bg_color', '#ffffff'),
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
                'footerCopy' => SiteSetting::get('footer_copyright', '© '.date('Y').' '.SiteSetting::get('site_name', 'DISPLAY LANKA.LK').'. All rights reserved.'),
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
            'heroTitle' => SiteSetting::get('hero_title', 'Get Premium Subscriptions'),
            'heroHighlight' => SiteSetting::get('hero_highlight_text', 'at Unbeatable Prices'),
            'heroSubtitle' => SiteSetting::get('hero_subtitle', 'Netflix, Spotify, Adobe and more'),
            'heroMicrocopy' => SiteSetting::get('hero_microcopy', 'Delivered to your inbox in seconds.'),
            'heroBgFrom' => SiteSetting::get('hero_bg_from', '#7c3aed'),
            'heroBgTo' => SiteSetting::get('hero_bg_to', '#4f46e5'),
            'heroLayout' => SiteSetting::get('hero_layout', 'split'),
            'heroAlignment' => SiteSetting::get('hero_alignment', 'left'),
            'heroSurface' => SiteSetting::get('hero_surface', 'soft'),
            'heroImagePath' => SiteSetting::get('hero_image_path', ''),
            'heroBtnText' => SiteSetting::get('hero_button_text', 'Shop Now'),
            'heroBtnLink' => SiteSetting::get('hero_button_link', '/products'),
            'featureOne' => SiteSetting::get('feature_one_text', 'Instant Delivery'),
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
            'finalCtaTitle' => SiteSetting::get('final_cta_title', 'Do not miss out — grab yours now!'),
            'finalCtaSubtitle' => SiteSetting::get('final_cta_subtitle', 'Instant delivery. Best prices in Sri Lanka.'),
            'finalCtaButtonText' => SiteSetting::get('final_cta_button_text', 'Browse Store'),
            'finalCtaButtonLink' => SiteSetting::get('final_cta_button_link', '/products'),
            'promoStripEnabled' => SiteSetting::get('promo_strip_enabled', true),
            'promoStripBadge' => SiteSetting::get('promo_strip_badge', 'Limited Drop'),
            'promoStripTitle' => SiteSetting::get('promo_strip_title', 'Build a stronger campaign story under the hero'),
            'promoStripText' => SiteSetting::get('promo_strip_text', 'Highlight free delivery, weekend discounts, or bundle offers in one strong promotional rail.'),
            'promoStripButtonText' => SiteSetting::get('promo_strip_button_text', 'Explore deals'),
            'promoStripButtonLink' => SiteSetting::get('promo_strip_button_link', '/products'),
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
        ]);
    }

    protected function getProductsByIds(string $prefix, array $ids): Collection
    {
        return $this->rememberOrCompute($prefix.md5(json_encode($ids)), 600, function () use ($ids) {
            return !empty($ids)
                ? Stock::with(['brand', 'category'])
                    ->whereIn('id', $ids)
                    ->where('status', 'active')
                    ->get()
                : collect();
        });
    }

    protected function rememberOrCompute(string $key, int $seconds, callable $callback): mixed
    {
        $scopedKey = app(TenantManager::class)->scopedCacheKey($key);

        try {
            return Cache::remember($scopedKey, $seconds, $callback);
        } catch (\Throwable) {
            return $callback();
        }
    }

    protected function enrichProducts(Collection $products): Collection
    {
        return $products->map(function (Stock $product) {
            $discount = $this->productPricingService->resolveDiscountForProduct($product);

            $product->setAttribute('final_price', $this->productPricingService->finalPriceForProduct($product));
            $product->setAttribute('primary_image_url', $this->productPricingService->imageUrlForProduct($product, 'card'));
            $product->setAttribute('primary_image_sources', $this->productPricingService->imageSourcesForProduct($product, 'card'));
            $product->setAttribute(
                'discount_badge',
                $discount
                    ? ($discount->type === 'percentage'
                        ? '-'.$discount->value.'%'
                        : '-Rs '.number_format((float) $discount->value, 0))
                    : null
            );

            return $product;
        });
    }
}

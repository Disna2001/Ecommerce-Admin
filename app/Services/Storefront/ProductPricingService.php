<?php

namespace App\Services\Storefront;

use App\Models\Discount;
use App\Models\Stock;
use Illuminate\Support\Facades\Cache;

class ProductPricingService
{
    public function __construct(
        protected StorefrontImageService $storefrontImageService
    ) {
    }

    public function resolveDiscountForProduct(Stock $product): ?Discount
    {
        return Cache::remember(
            'product_discount_'.$product->id.'_'.$product->category_id,
            300,
            function () use ($product) {
                return Discount::active()
                    ->where(function ($query) use ($product) {
                        $query->where('scope', 'all')
                            ->orWhere(fn($q) => $q->where('scope', 'product')->where('scope_id', $product->id))
                            ->orWhere(fn($q) => $q->where('scope', 'category')->where('scope_id', $product->category_id));
                    })
                    ->orderByDesc('value')
                    ->first();
            }
        );
    }

    public function finalPriceForProduct(Stock $product): float
    {
        $basePrice = (float) $product->selling_price;
        
        // Check for Automated Daily Discount
        $daily = $product->currentDailyDiscount;
        if ($daily) {
            $basePrice = (float) $daily->discounted_price;
        }

        $manualDiscount = $this->resolveDiscountForProduct($product);

        return $manualDiscount
            ? max(0, $basePrice - $manualDiscount->calculateDiscount($basePrice))
            : $basePrice;
    }

    public function toCartItem(Stock $product, int $quantity): array
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'price' => $this->finalPriceForProduct($product),
            'original_price' => (float) $product->selling_price,
            'quantity' => $quantity,
            'brand' => $product->brand?->name,
            'image' => $this->imageUrlForProduct($product),
        ];
    }

    public function imageUrlForProduct(Stock $product, string $preset = 'card'): ?string
    {
        $firstImage = collect($product->images)->first();
        return $firstImage
            ? $this->storefrontImageService->urlForPath($firstImage, $preset)
            : null;
    }

    public function imageSourcesForProduct(Stock $product, string $preset = 'card'): array
    {
        $firstImage = collect($product->images)->first();
        return $firstImage
            ? $this->storefrontImageService->pictureSourcesForPath($firstImage, $preset)
            : ['fallback' => null, 'webp' => null, 'jpeg' => null];
    }
}

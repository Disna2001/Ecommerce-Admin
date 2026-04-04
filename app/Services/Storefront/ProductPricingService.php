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
        $discount = $this->resolveDiscountForProduct($product);

        return $discount
            ? max(0, (float) $product->selling_price - $discount->calculateDiscount((float) $product->selling_price))
            : (float) $product->selling_price;
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
        return !empty($product->images)
            ? $this->storefrontImageService->urlForPath($product->images[0], $preset)
            : null;
    }

    public function imageSourcesForProduct(Stock $product, string $preset = 'card'): array
    {
        return !empty($product->images)
            ? $this->storefrontImageService->pictureSourcesForPath($product->images[0], $preset)
            : ['fallback' => null, 'webp' => null, 'jpeg' => null];
    }
}

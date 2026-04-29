<?php
// ============================================================
// app/Livewire/Shop/Wishlist.php
// ============================================================
namespace App\Livewire\Shop;

use Livewire\Component;
use App\Models\Stock;
use App\Services\Storefront\ProductPricingService;

class Wishlist extends Component
{
    public function getItemsProperty()
    {
        $ids = session('wishlist', []);
        $productPricingService = app(ProductPricingService::class);

        return empty($ids)
            ? collect()
            : Stock::with(['brand','category'])
                ->visibleOnStorefront()
                ->whereIn('id',$ids)
                ->get()
                ->map(function (Stock $product) use ($productPricingService) {
                    $discount = $productPricingService->resolveDiscountForProduct($product);
                    $product->setAttribute('final_price', $productPricingService->finalPriceForProduct($product));
                    $product->setAttribute('primary_image_url', $productPricingService->imageUrlForProduct($product, 'card'));
                    $product->setAttribute('primary_image_sources', $productPricingService->imageSourcesForProduct($product, 'card'));
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

    public function remove(int $id)
    {
        $list = array_values(array_diff(session('wishlist',[]), [$id]));
        session(['wishlist' => $list]);
        $this->dispatch('wishlist-updated', count: count($list));
        $this->dispatch('notify', type: 'info', message: 'Removed from wishlist.');
    }

    public function addToCart(int $id)
    {
        $productPricingService = app(ProductPricingService::class);
        $product = Stock::find($id);
        if (!$product || !$product->is_storefront_live) return;

        $cart = session('cart', []);
        $availableQuantity = $product->storefront_available_quantity;

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] = min($cart[$id]['quantity']+1, $availableQuantity);
        } else {
            $cart[$id] = $productPricingService->toCartItem($product, 1);
        }
        session(['cart'=>$cart]);
        $this->dispatch('cart-updated', count: collect($cart)->sum('quantity'));
        $this->dispatch('notify', type: 'success', message: 'Added to cart!');
    }

    public function addAllToCart()
    {
        foreach ($this->items as $product) {
            $this->addToCart($product->id);
        }
    }

    public function render()
    {
        return view('livewire.shop.wishlist');
    }
}

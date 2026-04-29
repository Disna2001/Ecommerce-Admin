<?php

namespace App\Livewire\Shop;

use Livewire\Component;
use App\Models\Stock;
use App\Models\Review;
use App\Services\Storefront\ProductPricingService;
use App\Services\Storefront\StorefrontImageService;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ProductDetail extends Component
{
    public Stock $product;
    public int   $quantity    = 1;
    public int   $activeImage = 0;
    public string $activeTab  = 'description';
    public bool  $inWishlist  = false;

    // Review form
    public int    $rating      = 5;
    public string $reviewTitle = '';
    public string $reviewBody  = '';

    protected $rules = [
        'rating'      => 'required|integer|min:1|max:5',
        'reviewTitle' => 'nullable|string|max:200',
        'reviewBody'  => 'required|string|min:10',
    ];

    public function mount(Stock $product)
    {
        abort_unless($product->is_storefront_live, 404);

        $this->product    = $product;
        $this->inWishlist = in_array($product->id, session('wishlist', []));
    }

    public function incrementQty()
    {
        if ($this->quantity < $this->product->storefront_available_quantity) $this->quantity++;
    }

    public function decrementQty()
    {
        if ($this->quantity > 1) $this->quantity--;
    }

    public function setImage(int $index)
    {
        $this->activeImage = $index;
    }

    public function setTab(string $tab)
    {
        $this->activeTab = $tab;
    }

    public function addToCart()
    {
        if (! $this->product->is_storefront_live) return;

        $cart = session('cart', []);
        $id   = $this->product->id;
        $productPricingService = app(ProductPricingService::class);
        $availableQuantity = $this->product->storefront_available_quantity;

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] = min($cart[$id]['quantity'] + $this->quantity, $availableQuantity);
        } else {
            $cart[$id] = $productPricingService->toCartItem($this->product, min($this->quantity, $availableQuantity));
        }
        session(['cart' => $cart]);

        $this->dispatch('cart-updated', count: collect($cart)->sum('quantity'));
        $this->dispatch('notify', type: 'success', message: 'Added to cart!');
    }

    public function buyNow()
    {
        $this->addToCart();

        if (!empty(session('cart'))) {
            return redirect()->route('checkout.index');
        }
    }

    public function toggleWishlist()
    {
        $wishlist = session('wishlist', []);
        $id       = $this->product->id;

        if (in_array($id, $wishlist)) {
            $wishlist = array_values(array_diff($wishlist, [$id]));
            $this->inWishlist = false;
            $this->dispatch('notify', type: 'info', message: 'Removed from wishlist.');
        } else {
            $wishlist[]       = $id;
            $this->inWishlist = true;
            $this->dispatch('notify', type: 'success', message: 'Added to wishlist!');
        }
        session(['wishlist' => $wishlist]);
        $this->dispatch('wishlist-updated', count: count($wishlist));
    }

    public function submitReview()
    {
        if (!auth()->check()) {
            $this->dispatch('notify', type: 'error', message: 'Please log in to write a review.');
            return;
        }

        $this->validate();

        if (class_exists(Review::class)) {
            $exists = Review::where('stock_id', $this->product->id)
                ->where('user_id', auth()->id())->exists();

            if ($exists) {
                $this->dispatch('notify', type: 'error', message: 'You already reviewed this product.');
                return;
            }

            Review::create([
                'stock_id'    => $this->product->id,
                'user_id'     => auth()->id(),
                'rating'      => $this->rating,
                'title'       => $this->reviewTitle,
                'body'        => $this->reviewBody,
                'is_approved' => true,
            ]);
        }

        $this->reset(['reviewTitle','reviewBody']);
        $this->rating = 5;
        $this->dispatch('notify', type: 'success', message: 'Review submitted!');
        $this->product->refresh();
        Cache::forget('product_reviews_'.$this->product->id);
    }

    public function render()
    {
        $productPricingService = app(ProductPricingService::class);
        $storefrontImageService = app(StorefrontImageService::class);
        $this->product->load(['category','brand','make','itemType','warranty','qualityLevel']);

        $discount = $productPricingService->resolveDiscountForProduct($this->product);

        $finalPrice = $discount
            ? max(0, $this->product->selling_price - $discount->calculateDiscount($this->product->selling_price))
            : null;

        $reviews = collect();
        if (class_exists(Review::class)) {
            $reviews = Cache::remember('product_reviews_'.$this->product->id, 300, function () {
                return Review::with('user')
                    ->where('stock_id', $this->product->id)
                    ->where('is_approved', true)
                    ->latest()
                    ->get();
            });
        }

        $related = Cache::remember('related_products_'.$this->product->id, 600, function () {
            return Stock::with('brand')
                ->visibleOnStorefront()
                ->where('category_id', $this->product->category_id)
                ->where('id','!=',$this->product->id)
                ->limit(4)
                ->get();
        });

        $related = $related->map(function (Stock $product) use ($productPricingService) {
            $product->setAttribute('primary_image_url', $productPricingService->imageUrlForProduct($product, 'card'));
            $product->setAttribute('primary_image_sources', $productPricingService->imageSourcesForProduct($product, 'card'));
            return $product;
        });

        $imageUrls = collect($this->product->images ?? [])
            ->map(fn($path) => $storefrontImageService->urlForPath($path, 'detail'))
            ->values();

        $thumbnailUrls = collect($this->product->images ?? [])
            ->map(fn($path) => $storefrontImageService->urlForPath($path, 'thumb'))
            ->values();

        $imageSourceSets = collect($this->product->images ?? [])
            ->map(fn($path) => $storefrontImageService->pictureSourcesForPath($path, 'detail'))
            ->values();

        $thumbnailSourceSets = collect($this->product->images ?? [])
            ->map(fn($path) => $storefrontImageService->pictureSourcesForPath($path, 'thumb'))
            ->values();

        $videoUrls = collect($this->product->videos ?? [])
            ->map(fn($path) => $path ? Storage::url($path) : null)
            ->filter()
            ->values();

        return view('livewire.shop.product-detail', compact('discount','finalPrice','reviews','related', 'imageUrls', 'thumbnailUrls', 'imageSourceSets', 'thumbnailSourceSets', 'videoUrls'))
            ->with('detailSettings', [
                'trust_cards' => [
                    ['title' => SiteSetting::get('detail_trust_one_title', 'Fast handling'), 'text' => SiteSetting::get('detail_trust_one_text', 'Orders are processed quickly with status updates sent to your email.')],
                    ['title' => SiteSetting::get('detail_trust_two_title', 'Payment confidence'), 'text' => SiteSetting::get('detail_trust_two_text', 'Bank and online payments are verified, and checkout keeps a full order trail.')],
                    ['title' => SiteSetting::get('detail_trust_three_title', 'Support ready'), 'text' => SiteSetting::get('detail_trust_three_text', 'You can track each step after ordering and get clear updates if verification is needed.')],
                ],
                'value_title' => SiteSetting::get('detail_value_title', 'Why shoppers choose this listing'),
                'value_text' => SiteSetting::get('detail_value_text', 'Clear pricing, direct checkout, and order notifications at each major status change.'),
                'value_cta' => SiteSetting::get('detail_value_cta', 'Checkout ready'),
                'in_stock_label' => SiteSetting::get('detail_in_stock_label', 'In Stock'),
                'low_stock_template' => SiteSetting::get('detail_low_stock_template', 'Only {quantity} left!'),
                'out_of_stock_label' => SiteSetting::get('detail_out_of_stock_label', 'Out of Stock'),
                'related_title' => SiteSetting::get('detail_related_title', 'Related Products'),
                'show_reviews' => SiteSetting::get('detail_show_reviews', true),
                'show_related' => SiteSetting::get('detail_show_related', true),
            ]);
    }
}

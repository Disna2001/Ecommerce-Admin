<?php

namespace App\Livewire\Shop;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Stock;
use App\Models\Category;
use App\Models\Brand;
use App\Services\Storefront\ProductPricingService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class ProductList extends Component
{
    use WithPagination;

    public string $search       = '';
    public string $sort         = 'newest';
    public string $category     = '';
    public string $brand        = '';
    public string $min_price    = '';
    public string $max_price    = '';
    public int    $perPage      = 16;
    public bool   $showFilters  = false;

    protected $queryString = [
        'search'    => ['except' => ''],
        'sort'      => ['except' => 'newest'],
        'category'  => ['except' => ''],
        'brand'     => ['except' => ''],
        'min_price' => ['except' => ''],
        'max_price' => ['except' => ''],
    ];

    public function updatingSearch()   { $this->resetPage(); }
    public function updatingSort()     { $this->resetPage(); }
    public function updatingCategory() { $this->resetPage(); }
    public function updatingBrand()    { $this->resetPage(); }
    public function updatingMinPrice() { $this->resetPage(); }
    public function updatingMaxPrice() { $this->resetPage(); }

    public function clearFilters()
    {
        $this->reset(['search','sort','category','brand','min_price','max_price']);
        $this->resetPage();
    }

    public function addToCart(int $id)
    {
        $productPricingService = app(ProductPricingService::class);
        $product = Stock::with('brand')->find($id);
        if (!$product || $product->quantity <= 0) return;

        $cart = session('cart', []);

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] = min($cart[$id]['quantity'] + 1, $product->quantity);
        } else {
            $cart[$id] = $productPricingService->toCartItem($product, 1);
        }
        session(['cart' => $cart]);

        $this->dispatch('cart-updated', count: collect($cart)->sum('quantity'));
        $this->dispatch('notify', type: 'success', message: $product->name . ' added to cart!');
    }

    public function toggleWishlist(int $id)
    {
        $wishlist = session('wishlist', []);
        $added = false;
        if (in_array($id, $wishlist)) {
            $wishlist = array_values(array_diff($wishlist, [$id]));
        } else {
            $wishlist[] = $id;
            $added = true;
        }
        session(['wishlist' => $wishlist]);

        $this->dispatch('wishlist-updated', count: count($wishlist));
        $this->dispatch('notify', type: $added ? 'success' : 'info',
            message: $added ? 'Added to wishlist!' : 'Removed from wishlist.');
    }

    public function render()
    {
        $productPricingService = app(ProductPricingService::class);
        $cacheKey = 'product_list_results_'.md5(json_encode([
            'search' => $this->search,
            'sort' => $this->sort,
            'category' => $this->category,
            'brand' => $this->brand,
            'min_price' => $this->min_price,
            'max_price' => $this->max_price,
            'per_page' => $this->perPage,
            'page' => request()->query('page', 1),
        ]));

        $query = Stock::with(['category','brand'])
            ->where('status','active')
            ->when($this->search, fn($q) => $q->where(function (Builder $searchQuery) {
                $searchQuery
                    ->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('sku', 'like', '%'.$this->search.'%')
                    ->orWhere('model_name', 'like', '%'.$this->search.'%');
            }))
            ->when($this->category, fn($q) => $q->where('category_id', $this->category))
            ->when($this->brand,    fn($q) => $q->where('brand_id',    $this->brand))
            ->when($this->min_price, fn($q) => $q->where('selling_price','>=',$this->min_price))
            ->when($this->max_price, fn($q) => $q->where('selling_price','<=',$this->max_price));

        match($this->sort) {
            'price_asc'  => $query->orderBy('selling_price','asc'),
            'price_desc' => $query->orderBy('selling_price','desc'),
            'name'       => $query->orderBy('name','asc'),
            default      => $query->orderByDesc('created_at'),
        };

        $products = Cache::remember($cacheKey, 120, fn() => $query->paginate($this->perPage));

        $products->getCollection()->transform(function (Stock $product) use ($productPricingService) {
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

        return view('livewire.shop.product-list', [
            'products'   => $products,
            'categories' => Cache::remember('product_list_categories', 600, fn() => Category::query()->select('id', 'name')->orderBy('name')->get()),
            'brands'     => Cache::remember('product_list_brands', 600, fn() => Brand::query()->select('id', 'name')->orderBy('name')->get()),
            'wishlist'   => session('wishlist', []),
        ]);
    }
}

@php
    use App\Models\SiteSetting;
    use App\Models\Stock;
    use App\Models\Category;
    use App\Models\Brand;
    use App\Models\Discount;
    use Illuminate\Support\Facades\Storage;

    $siteName       = SiteSetting::get('site_name',      'DISPLAY LANKA.LK');
    $logoPath       = SiteSetting::get('logo_path',      '');
    $primaryColor   = SiteSetting::get('primary_color',  '#4f46e5');
    $secondaryColor = SiteSetting::get('secondary_color','#7c3aed');
    $textColor      = SiteSetting::get('text_color',     '#111827');
    $bgColor        = SiteSetting::get('bg_color',       '#f9fafb');
    $navBgColor     = SiteSetting::get('nav_bg_color',   '#ffffff');
    $topbarEnabled  = SiteSetting::get('topbar_enabled', true);
    $topbarText     = SiteSetting::get('topbar_text',    'Sale! Up to 50% off selected items.');
    $topbarFrom     = SiteSetting::get('topbar_bg_from', '#7c3aed');
    $topbarTo       = SiteSetting::get('topbar_bg_to',   '#4f46e5');

    $categories = Category::all();
    $brands     = Brand::all();

    $query = Stock::with(['category','brand'])
        ->where('status','active');

    if(request('category')) $query->where('category_id', request('category'));
    if(request('brand'))    $query->where('brand_id',    request('brand'));
    if(request('search'))   $query->where('name','like','%'.request('search').'%');
    if(request('min_price')) $query->where('selling_price','>=', request('min_price'));
    if(request('max_price')) $query->where('selling_price','<=', request('max_price'));

    $sort = request('sort','newest');
    match($sort) {
        'price_asc'  => $query->orderBy('selling_price','asc'),
        'price_desc' => $query->orderBy('selling_price','desc'),
        'name'       => $query->orderBy('name','asc'),
        default      => $query->orderByDesc('created_at'),
    };

    $products = $query->paginate(16)->withQueryString();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Products — {{ $siteName }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css','resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>window._token = '{{ csrf_token() }}';</script>
    <style>
        :root { --c-primary:{{ $primaryColor }};--c-secondary:{{ $secondaryColor }};--c-text:{{ $textColor }};--c-bg:{{ $bgColor }};--c-nav:{{ $navBgColor }}; }
        body { background:var(--c-bg);color:var(--c-text); }
        .btn-primary { background:var(--c-primary);color:#fff; }
        .btn-primary:hover { filter:brightness(.88); }
        .hover-primary:hover { color:var(--c-primary); }
        .ring-primary:focus { outline:none;box-shadow:0 0 0 3px color-mix(in srgb,var(--c-primary) 30%,transparent); }
        .card-hover { transition:all .25s ease; }
        .card-hover:hover { transform:translateY(-4px);box-shadow:0 20px 40px rgba(0,0,0,.10); }
        .filter-active { background:var(--c-primary);color:#fff;border-color:var(--c-primary); }
    </style>
</head>
<body class="font-sans antialiased">

{{-- TOP BAR --}}
@if($topbarEnabled)
<div class="py-2 text-center text-sm text-white font-medium"
     style="background:linear-gradient(to right,{{ $topbarFrom }},{{ $topbarTo }})">
    <i class="fas fa-tag mr-2"></i>{{ $topbarText }}
</div>
@endif

{{-- NAV --}}
@include('frontend.partials.nav', compact('siteName','logoPath','primaryColor','secondaryColor','textColor','navBgColor'))

<div class="container mx-auto px-4 py-8">

    {{-- BREADCRUMB --}}
    <nav class="flex items-center gap-2 text-sm text-gray-400 mb-6">
        <a href="/" class="hover-primary">Home</a>
        <i class="fas fa-chevron-right text-xs"></i>
        <span class="text-gray-700 font-medium">All Products</span>
    </nav>

    <div class="flex gap-8">

        {{-- SIDEBAR FILTERS --}}
        <aside class="hidden lg:block w-64 flex-shrink-0">
            <form method="GET" action="{{ url('/products') }}">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-24">

                    <div class="flex items-center justify-between mb-5">
                        <h3 class="font-bold text-gray-900 text-lg">Filters</h3>
                        <a href="{{ url('/products') }}" class="text-xs text-gray-400 hover:text-red-500">Clear all</a>
                    </div>

                    {{-- Search --}}
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Search</label>
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}"
                                   placeholder="Search products..."
                                   class="w-full pl-9 pr-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-indigo-400">
                            <i class="fas fa-search absolute left-3 top-2.5 text-gray-300 text-xs"></i>
                        </div>
                    </div>

                    {{-- Categories --}}
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Category</label>
                        <div class="space-y-2">
                            @foreach($categories as $cat)
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="radio" name="category" value="{{ $cat->id }}"
                                       {{ request('category') == $cat->id ? 'checked' : '' }}
                                       class="accent-indigo-600">
                                <span class="text-sm text-gray-600 group-hover:text-gray-900">{{ $cat->name }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Price Range --}}
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Price Range (Rs)</label>
                        <div class="flex gap-2">
                            <input type="number" name="min_price" value="{{ request('min_price') }}"
                                   placeholder="Min"
                                   class="w-full border border-gray-200 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:border-indigo-400">
                            <input type="number" name="max_price" value="{{ request('max_price') }}"
                                   placeholder="Max"
                                   class="w-full border border-gray-200 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:border-indigo-400">
                        </div>
                    </div>

                    {{-- Brands --}}
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Brand</label>
                        <div class="space-y-2 max-h-40 overflow-y-auto">
                            @foreach($brands as $brand)
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="radio" name="brand" value="{{ $brand->id }}"
                                       {{ request('brand') == $brand->id ? 'checked' : '' }}
                                       class="accent-indigo-600">
                                <span class="text-sm text-gray-600 group-hover:text-gray-900">{{ $brand->name }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <button type="submit" class="w-full btn-primary py-2.5 rounded-xl font-semibold text-sm">
                        Apply Filters
                    </button>
                </div>
            </form>
        </aside>

        {{-- PRODUCT GRID --}}
        <div class="flex-1 min-w-0">

            {{-- Top Bar --}}
            <div class="flex items-center justify-between mb-6">
                <p class="text-sm text-gray-500">
                    Showing <strong class="text-gray-900">{{ $products->total() }}</strong> products
                </p>
                <div class="flex items-center gap-3">
                    <label class="text-sm text-gray-500">Sort:</label>
                    <select name="sort" onchange="window.location='{{ url('/products') }}?'+new URLSearchParams({...Object.fromEntries(new URLSearchParams(location.search)),...{sort:this.value}}).toString()"
                            class="border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none">
                        <option value="newest"     {{ $sort==='newest'     ? 'selected':'' }}>Newest</option>
                        <option value="price_asc"  {{ $sort==='price_asc'  ? 'selected':'' }}>Price: Low to High</option>
                        <option value="price_desc" {{ $sort==='price_desc' ? 'selected':'' }}>Price: High to Low</option>
                        <option value="name"       {{ $sort==='name'       ? 'selected':'' }}>Name A-Z</option>
                    </select>
                </div>
            </div>

            {{-- Grid --}}
            <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-5">
                @forelse($products as $product)
                @php
                    $discount = Discount::active()
                        ->where(fn($q) => $q->where('scope','all')
                            ->orWhere(fn($q2) => $q2->where('scope','product')->where('scope_id',$product->id))
                            ->orWhere(fn($q2) => $q2->where('scope','category')->where('scope_id',$product->category_id)))
                        ->orderByDesc('value')->first();
                    $finalPrice = $discount ? max(0, $product->selling_price - $discount->calculateDiscount($product->selling_price)) : null;
                @endphp
                <a href="{{ url('/products/'.$product->id) }}" class="group card-hover bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm flex flex-col">
                    <div class="relative h-48 bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center overflow-hidden">
                        @if(!empty($product->images) && count($product->images))
                            <img src="{{ Storage::url($product->images[0]) }}" alt="{{ $product->name }}"
                                 class="w-full h-48 object-cover group-hover:scale-105 transition duration-300">
                        @else
                            <i class="fas fa-box text-5xl text-gray-200"></i>
                        @endif
                        {{-- Wishlist --}}
                        <button type="button"
                                class="shop-wishlist-btn absolute top-3 right-3 w-8 h-8 bg-white rounded-full shadow flex items-center justify-center hover:scale-110 transition"
                                data-id="{{ $product->id }}">
                            <i class="far fa-heart text-gray-400 text-sm"></i>
                        </button>
                        @if($discount)
                        <span class="absolute top-3 left-3 bg-green-500 text-white text-xs font-bold px-2 py-1 rounded-lg">
                            {{ $discount->type==='percentage' ? '-'.$discount->value.'%' : '-Rs '.number_format($discount->value,0) }}
                        </span>
                        @endif
                        @if($product->isLowStock())
                        <span class="absolute bottom-3 left-3 bg-orange-500 text-white text-xs font-semibold px-2 py-1 rounded-lg">
                            Only {{ $product->quantity }} left
                        </span>
                        @endif
                    </div>
                    <div class="p-4 flex flex-col flex-1">
                        <p class="text-xs text-gray-400 mb-1">{{ $product->brand->name ?? $product->category->name ?? '' }}</p>
                        <h3 class="font-semibold text-gray-900 text-sm leading-snug mb-2 flex-1 line-clamp-2">{{ $product->name }}</h3>
                        <div class="flex items-center gap-1 mb-3">
                            @for($i=1;$i<=5;$i++)
                            <i class="fas fa-star text-yellow-400 text-xs"></i>
                            @endfor
                            <span class="text-xs text-gray-400 ml-1">(0)</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div>
                                @if($finalPrice !== null)
                                    <span class="font-bold text-gray-900">Rs {{ number_format($finalPrice,2) }}</span>
                                    <span class="text-xs text-gray-400 line-through ml-1">Rs {{ number_format($product->selling_price,2) }}</span>
                                @else
                                    <span class="font-bold text-gray-900">Rs {{ number_format($product->selling_price,2) }}</span>
                                @endif
                            </div>
                            <button type="button"
                                    class="shop-cart-btn btn-primary w-8 h-8 rounded-xl flex items-center justify-center hover:scale-110 transition"
                                    data-id="{{ $product->id }}">
                                <i class="fas fa-plus text-xs"></i>
                            </button>
                        </div>
                    </div>
                </a>
                @empty
                <div class="col-span-4 py-24 text-center text-gray-400">
                    <i class="fas fa-search text-5xl mb-4 block"></i>
                    <p class="text-lg font-medium">No products found</p>
                    <p class="text-sm mt-1">Try adjusting your filters</p>
                    <a href="{{ url('/products') }}" class="inline-block mt-4 btn-primary px-6 py-2 rounded-xl text-sm font-semibold">Clear Filters</a>
                </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="mt-10">{{ $products->links() }}</div>
        </div>
    </div>
</div>

@include('frontend.partials.footer', compact('siteName','logoPath','primaryColor','secondaryColor','textColor','bgColor'))

<script>
document.addEventListener('click', function(e) {
    const cartBtn = e.target.closest('.shop-cart-btn');
    if (cartBtn) {
        e.preventDefault();
        const id   = cartBtn.dataset.id;
        const icon = cartBtn.querySelector('i');
        icon.className = 'fas fa-spinner fa-spin text-xs';
        fetch('/cart/add/' + id, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': window._token, 'Content-Type': 'application/json' },
            body: JSON.stringify({ quantity: 1 })
        }).then(r => r.json()).then(d => {
            icon.className = 'fas fa-check text-xs';
            document.querySelectorAll('.cart-count').forEach(el => {
                el.textContent = d.count ?? '';
                el.classList.toggle('hidden', !(d.count > 0));
            });
            setTimeout(() => { icon.className = 'fas fa-plus text-xs'; }, 1500);
        }).catch(() => { icon.className = 'fas fa-plus text-xs'; });
        return;
    }

    const wishBtn = e.target.closest('.shop-wishlist-btn');
    if (wishBtn) {
        e.preventDefault();
        const id   = wishBtn.dataset.id;
        const icon = wishBtn.querySelector('i');
        fetch('/wishlist/toggle/' + id, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': window._token }
        }).then(r => r.json()).then(d => {
            icon.className = d.added
                ? 'fas fa-heart text-red-500 text-sm'
                : 'far fa-heart text-gray-400 text-sm';
            document.querySelectorAll('.wishlist-count').forEach(el => {
                el.textContent = d.count ?? '';
                el.classList.toggle('hidden', !(d.count > 0));
            });
        }).catch(() => {});
    }
});
</script>
</body>
</html>

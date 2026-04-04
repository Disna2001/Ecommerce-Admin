@php
    use App\Models\SiteSetting;
    use App\Models\Stock;
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

    // $product is passed from controller
    $discount = Discount::active()
        ->where(fn($q) => $q->where('scope','all')
            ->orWhere(fn($q2) => $q2->where('scope','product')->where('scope_id',$product->id))
            ->orWhere(fn($q2) => $q2->where('scope','category')->where('scope_id',$product->category_id)))
        ->orderByDesc('value')->first();
    $finalPrice = $discount ? max(0, $product->selling_price - $discount->calculateDiscount($product->selling_price)) : null;

    $related = Stock::with('brand')
        ->where('category_id', $product->category_id)
        ->where('id','!=',$product->id)
        ->where('status','active')
        ->limit(4)->get();

    // Reviews — placeholder collection until Review model exists
    $reviews = collect([]);
    if(class_exists(\App\Models\Review::class)) {
        $reviews = \App\Models\Review::with('user')
            ->where('stock_id', $product->id)
            ->where('is_approved', true)
            ->latest()->get();
    }
    $avgRating  = $reviews->avg('rating') ?? 0;
    $totalReviews = $reviews->count();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $product->name }} — {{ $siteName }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css','resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>window._token = '{{ csrf_token() }}';</script>
    <style>
        :root{--c-primary:{{ $primaryColor }};--c-secondary:{{ $secondaryColor }};--c-text:{{ $textColor }};--c-bg:{{ $bgColor }};--c-nav:{{ $navBgColor }};}
        body{background:var(--c-bg);color:var(--c-text);}
        .btn-primary{background:var(--c-primary);color:#fff;}
        .btn-primary:hover{filter:brightness(.88);}
        .hover-primary:hover{color:var(--c-primary);}
        .thumb-active{border-color:var(--c-primary)!important;}
        .star-input input{display:none;}
        .star-input label{cursor:pointer;color:#d1d5db;font-size:1.5rem;}
        .star-input label:hover,.star-input label:hover~label,.star-input input:checked~label{color:#f59e0b;}
        .star-input{display:flex;flex-direction:row-reverse;justify-content:flex-end;}
        .rating-bar{height:8px;border-radius:4px;background:#e5e7eb;}
        .rating-bar-fill{height:8px;border-radius:4px;background:#f59e0b;}
        .tab-active{border-bottom:2px solid var(--c-primary);color:var(--c-primary);}
        .qty-btn:hover{background:var(--c-primary);color:#fff;}
    </style>
</head>
<body class="font-sans antialiased">

@if($topbarEnabled)
<div class="py-2 text-center text-sm text-white font-medium"
     style="background:linear-gradient(to right,{{ $topbarFrom }},{{ $topbarTo }})">
    <i class="fas fa-tag mr-2"></i>{{ $topbarText }}
</div>
@endif

@include('frontend.partials.nav', compact('siteName','logoPath','primaryColor','secondaryColor','textColor','navBgColor'))

<div class="container mx-auto px-4 py-8 max-w-7xl">

    {{-- BREADCRUMB --}}
    <nav class="flex items-center gap-2 text-sm text-gray-400 mb-8">
        <a href="/" class="hover-primary">Home</a>
        <i class="fas fa-chevron-right text-xs"></i>
        <a href="{{ url('/products') }}" class="hover-primary">Products</a>
        <i class="fas fa-chevron-right text-xs"></i>
        <span class="text-gray-700 font-medium truncate max-w-xs">{{ $product->name }}</span>
    </nav>

    {{-- PRODUCT MAIN --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-16">

        {{-- IMAGE GALLERY --}}
        <div>
            <div class="bg-white rounded-3xl overflow-hidden border border-gray-100 shadow-sm mb-4 flex items-center justify-center h-96">
                @if(!empty($product->images) && count($product->images))
                    <img id="mainImage"
                         src="{{ Storage::url($product->images[0]) }}"
                         alt="{{ $product->name }}"
                         class="w-full h-96 object-cover">
                @else
                    <div class="flex flex-col items-center text-gray-200">
                        <i class="fas fa-box text-8xl mb-3"></i>
                        <span class="text-sm text-gray-300">No image available</span>
                    </div>
                @endif
            </div>
            {{-- Thumbnails --}}
            @if(!empty($product->images) && count($product->images) > 1)
            <div class="flex gap-3 overflow-x-auto pb-2">
                @foreach($product->images as $i => $img)
                <button onclick="document.getElementById('mainImage').src='{{ Storage::url($img) }}';document.querySelectorAll('.thumb').forEach(t=>t.classList.remove('thumb-active'));this.classList.add('thumb-active')"
                        class="thumb flex-shrink-0 w-20 h-20 rounded-xl overflow-hidden border-2 border-transparent hover:border-indigo-400 transition {{ $i===0 ? 'thumb-active' : '' }}">
                    <img src="{{ Storage::url($img) }}" class="w-full h-full object-cover">
                </button>
                @endforeach
            </div>
            @endif
        </div>

        {{-- PRODUCT INFO --}}
        <div>
            {{-- Brand & Category --}}
            <div class="flex items-center gap-3 mb-3">
                @if($product->brand)
                <span class="text-sm font-semibold px-3 py-1 rounded-full" style="background:{{ $primaryColor }}15;color:{{ $primaryColor }}">
                    {{ $product->brand->name }}
                </span>
                @endif
                @if($product->category)
                <span class="text-sm text-gray-400">{{ $product->category->name }}</span>
                @endif
                @if($product->qualityLevel)
                <span class="text-xs font-semibold px-2 py-1 rounded-full"
                      style="background:{{ $product->qualityLevel->color }}20;color:{{ $product->qualityLevel->color }}">
                    {{ $product->qualityLevel->name }}
                </span>
                @endif
            </div>

            <h1 class="text-3xl font-extrabold text-gray-900 leading-tight mb-3">{{ $product->name }}</h1>

            {{-- SKU & Model --}}
            <p class="text-sm text-gray-400 mb-4">
                SKU: <span class="font-mono">{{ $product->sku }}</span>
                @if($product->model_name) · {{ $product->model_name }} @endif
                @if($product->model_number) <span class="font-mono">#{{ $product->model_number }}</span> @endif
            </p>

            {{-- Rating Summary --}}
            <div class="flex items-center gap-3 mb-5">
                <div class="flex">
                    @for($i=1;$i<=5;$i++)
                    <i class="fa{{ $i <= round($avgRating) ? 's' : 'r' }} fa-star text-yellow-400"></i>
                    @endfor
                </div>
                <span class="text-sm font-semibold text-gray-700">{{ number_format($avgRating,1) }}</span>
                <span class="text-sm text-gray-400">({{ $totalReviews }} {{ Str::plural('review',$totalReviews) }})</span>
                <a href="#reviews" class="text-sm hover-primary underline" style="color:{{ $primaryColor }}">Write a review</a>
            </div>

            {{-- Price --}}
            <div class="flex items-end gap-4 mb-6 p-4 bg-gray-50 rounded-2xl">
                @if($finalPrice !== null)
                    <span class="text-4xl font-extrabold text-gray-900">Rs {{ number_format($finalPrice,2) }}</span>
                    <span class="text-xl text-gray-400 line-through">Rs {{ number_format($product->selling_price,2) }}</span>
                    <span class="text-sm font-bold text-green-600 bg-green-100 px-2 py-1 rounded-lg">
                        {{ $discount->type==='percentage' ? $discount->value.'% OFF' : 'Rs '.number_format($discount->value,2).' OFF' }}
                    </span>
                @else
                    <span class="text-4xl font-extrabold text-gray-900">Rs {{ number_format($product->selling_price,2) }}</span>
                @endif
            </div>

            {{-- Attributes --}}
            <div class="grid grid-cols-2 gap-3 mb-6 text-sm">
                @if($product->color)
                <div class="flex items-center gap-2 bg-white rounded-xl p-3 border border-gray-100">
                    <i class="fas fa-palette text-gray-400"></i>
                    <span class="text-gray-500">Color:</span>
                    <span class="font-medium">{{ $product->color }}</span>
                </div>
                @endif
                @if($product->size)
                <div class="flex items-center gap-2 bg-white rounded-xl p-3 border border-gray-100">
                    <i class="fas fa-ruler text-gray-400"></i>
                    <span class="text-gray-500">Size:</span>
                    <span class="font-medium">{{ $product->size }}</span>
                </div>
                @endif
                @if($product->weight)
                <div class="flex items-center gap-2 bg-white rounded-xl p-3 border border-gray-100">
                    <i class="fas fa-weight text-gray-400"></i>
                    <span class="text-gray-500">Weight:</span>
                    <span class="font-medium">{{ $product->weight }}kg</span>
                </div>
                @endif
                @if($product->warranty)
                <div class="flex items-center gap-2 bg-white rounded-xl p-3 border border-gray-100">
                    <i class="fas fa-shield-alt text-gray-400"></i>
                    <span class="text-gray-500">Warranty:</span>
                    <span class="font-medium">{{ $product->warranty->name }}</span>
                </div>
                @endif
            </div>

            {{-- Stock Status --}}
            <div class="flex items-center gap-2 mb-6">
                @if($product->quantity > 0)
                    <span class="w-2.5 h-2.5 bg-green-500 rounded-full inline-block animate-pulse"></span>
                    <span class="text-sm font-medium text-green-600">
                        In Stock
                        @if($product->isLowStock()) — Only {{ $product->quantity }} left! @endif
                    </span>
                @else
                    <span class="w-2.5 h-2.5 bg-red-500 rounded-full inline-block"></span>
                    <span class="text-sm font-medium text-red-600">Out of Stock</span>
                @endif
            </div>

            {{-- Qty + Cart + Wishlist --}}
            <div class="flex items-center gap-4 mb-6">
                <div class="flex items-center border border-gray-200 rounded-xl overflow-hidden">
                    <button onclick="changeQty(-1)" class="qty-btn w-10 h-12 flex items-center justify-center text-gray-600 hover:bg-gray-100 transition font-bold text-lg">−</button>
                    <input id="qty" type="number" value="1" min="1" max="{{ $product->quantity }}"
                           class="w-14 h-12 text-center text-sm font-semibold border-0 focus:outline-none">
                    <button onclick="changeQty(1)" class="qty-btn w-10 h-12 flex items-center justify-center text-gray-600 hover:bg-gray-100 transition font-bold text-lg">+</button>
                </div>

                <button onclick="addToCart({{ $product->id }})"
                        id="addToCartBtn"
                        class="flex-1 btn-primary py-3 rounded-2xl font-bold text-base flex items-center justify-center gap-2 transition"
                        {{ $product->quantity <= 0 ? 'disabled' : '' }}>
                    <i class="fas fa-shopping-cart"></i> Add to Cart
                </button>

                <button onclick="toggleWishlist({{ $product->id }},this)"
                        class="w-12 h-12 border border-gray-200 rounded-2xl flex items-center justify-center hover:border-red-400 hover:text-red-500 transition wishlist-btn"
                        data-id="{{ $product->id }}">
                    <i class="far fa-heart text-gray-400"></i>
                </button>
            </div>

            {{-- Tags --}}
            @if($product->tags)
            <div class="flex flex-wrap gap-2">
                @foreach(explode(',', $product->tags) as $tag)
                <span class="text-xs px-3 py-1 rounded-full bg-gray-100 text-gray-500 hover:bg-gray-200 transition cursor-pointer">
                    #{{ trim($tag) }}
                </span>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    {{-- TABS: Description / Specs / Reviews --}}
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm mb-16" id="reviews">
        <div class="flex border-b border-gray-100 px-6">
            @foreach(['description' => 'Description', 'specs' => 'Specifications', 'reviews' => 'Reviews ('.$totalReviews.')'] as $tab => $label)
            <button onclick="switchTab('{{ $tab }}')"
                    id="tab-{{ $tab }}"
                    class="px-6 py-4 text-sm font-semibold text-gray-500 hover:text-gray-900 transition border-b-2 border-transparent -mb-px {{ $tab==='description' ? 'tab-active' : '' }}">
                {{ $label }}
            </button>
            @endforeach
        </div>

        {{-- Description Tab --}}
        <div id="panel-description" class="p-8">
            <div class="prose max-w-none text-gray-600 leading-relaxed">
                {!! nl2br(e($product->description ?? 'No description available.')) !!}
            </div>
            @if($product->notes)
            <div class="mt-6 p-4 bg-blue-50 rounded-xl border border-blue-100">
                <p class="text-sm font-semibold text-blue-700 mb-1"><i class="fas fa-info-circle mr-2"></i>Additional Notes</p>
                <p class="text-sm text-blue-600">{{ $product->notes }}</p>
            </div>
            @endif
        </div>

        {{-- Specs Tab --}}
        <div id="panel-specs" class="p-8 hidden">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach(array_filter([
                    'SKU'          => $product->sku,
                    'Item Code'    => $product->item_code,
                    'Brand'        => $product->brand?->name,
                    'Make'         => $product->make?->name,
                    'Model'        => $product->model_name,
                    'Model Number' => $product->model_number,
                    'Color'        => $product->color,
                    'Size'         => $product->size,
                    'Weight'       => $product->weight ? $product->weight.'kg' : null,
                    'Category'     => $product->category?->name,
                    'Item Type'    => $product->itemType?->name,
                    'Warranty'     => $product->warranty?->name,
                    'Quality Level'=> $product->qualityLevel?->name,
                    'Barcode'      => $product->barcode,
                ]) as $label => $value)
                <div class="flex justify-between py-3 border-b border-gray-50">
                    <span class="text-sm text-gray-500 font-medium">{{ $label }}</span>
                    <span class="text-sm text-gray-900 font-semibold">{{ $value }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Reviews Tab --}}
        <div id="panel-reviews" class="p-8 hidden">

            {{-- Rating Summary --}}
            <div class="flex flex-col md:flex-row gap-10 mb-10">
                <div class="text-center md:w-48 flex-shrink-0">
                    <div class="text-7xl font-black text-gray-900">{{ number_format($avgRating,1) }}</div>
                    <div class="flex justify-center gap-1 my-2">
                        @for($i=1;$i<=5;$i++)
                        <i class="fa{{ $i <= round($avgRating) ? 's' : 'r' }} fa-star text-yellow-400 text-xl"></i>
                        @endfor
                    </div>
                    <p class="text-sm text-gray-400">{{ $totalReviews }} {{ Str::plural('review', $totalReviews) }}</p>
                </div>
                <div class="flex-1 space-y-2">
                    @for($star=5;$star>=1;$star--)
                    @php $count = $reviews->where('rating',$star)->count(); $pct = $totalReviews ? round($count/$totalReviews*100) : 0; @endphp
                    <div class="flex items-center gap-3">
                        <span class="text-xs text-gray-500 w-4">{{ $star }}</span>
                        <i class="fas fa-star text-yellow-400 text-xs"></i>
                        <div class="flex-1 rating-bar"><div class="rating-bar-fill" style="width:{{ $pct }}%"></div></div>
                        <span class="text-xs text-gray-400 w-8">{{ $count }}</span>
                    </div>
                    @endfor
                </div>
            </div>

            {{-- Review List --}}
            @forelse($reviews as $review)
            <div class="border-b border-gray-50 pb-6 mb-6 last:border-0">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0"
                         style="background:{{ $primaryColor }}">
                        {{ strtoupper(substr($review->user->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-semibold text-gray-900">{{ $review->user->name ?? 'Anonymous' }}</span>
                            <span class="text-xs text-gray-400">{{ $review->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="flex gap-0.5 mb-2">
                            @for($i=1;$i<=5;$i++)
                            <i class="fa{{ $i <= $review->rating ? 's' : 'r' }} fa-star text-yellow-400 text-xs"></i>
                            @endfor
                        </div>
                        @if($review->title)
                        <p class="font-medium text-gray-800 mb-1">{{ $review->title }}</p>
                        @endif
                        <p class="text-sm text-gray-600 leading-relaxed">{{ $review->body }}</p>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-12 text-gray-400">
                <i class="fas fa-comment-slash text-4xl mb-3 block"></i>
                <p class="font-medium">No reviews yet</p>
                <p class="text-sm mt-1">Be the first to review this product</p>
            </div>
            @endforelse

            {{-- Write Review Form --}}
            @auth
            <div class="mt-8 bg-gray-50 rounded-2xl p-6">
                <h4 class="font-bold text-gray-900 text-lg mb-5">Write a Review</h4>
                <form action="{{ url('/products/'.$product->id.'/reviews') }}" method="POST">
                    @csrf
                    <input type="hidden" name="stock_id" value="{{ $product->id }}">

                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Your Rating *</label>
                        <div class="star-input" id="starInput">
                            @for($i=5;$i>=1;$i--)
                            <input type="radio" name="rating" id="star{{ $i }}" value="{{ $i }}" {{ $i===5 ? 'required' : '' }}>
                            <label for="star{{ $i }}"><i class="fas fa-star"></i></label>
                            @endfor
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Review Title</label>
                        <input type="text" name="title" placeholder="Summarize your experience"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-indigo-400">
                    </div>

                    <div class="mb-5">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Your Review *</label>
                        <textarea name="body" rows="4" required placeholder="Share your thoughts about this product..."
                                  class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-indigo-400 resize-none"></textarea>
                    </div>

                    <button type="submit" class="btn-primary px-8 py-2.5 rounded-xl font-semibold text-sm">
                        <i class="fas fa-paper-plane mr-2"></i>Submit Review
                    </button>
                </form>
            </div>
            @else
            <div class="mt-6 text-center p-6 bg-gray-50 rounded-2xl">
                <p class="text-gray-600 mb-3">Please log in to write a review</p>
                <a href="{{ route('login') }}" class="btn-primary px-6 py-2.5 rounded-xl font-semibold text-sm inline-block">Log In</a>
            </div>
            @endauth
        </div>
    </div>

    {{-- RELATED PRODUCTS --}}
    @if($related->isNotEmpty())
    <div class="mb-16">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Related Products</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
            @foreach($related as $rp)
            <a href="{{ url('/products/'.$rp->id) }}"
               class="group bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm hover:-translate-y-1 hover:shadow-lg transition-all">
                <div class="h-40 bg-gray-50 flex items-center justify-center overflow-hidden">
                    @if(!empty($rp->images) && count($rp->images))
                        <img src="{{ Storage::url($rp->images[0]) }}" class="w-full h-40 object-cover group-hover:scale-105 transition">
                    @else
                        <i class="fas fa-box text-4xl text-gray-200"></i>
                    @endif
                </div>
                <div class="p-4">
                    <p class="text-xs text-gray-400 mb-1">{{ $rp->brand->name ?? '' }}</p>
                    <h4 class="font-semibold text-sm text-gray-900 line-clamp-2">{{ $rp->name }}</h4>
                    <p class="font-bold text-gray-900 mt-2">Rs {{ number_format($rp->selling_price,2) }}</p>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

</div>

@include('frontend.partials.footer', compact('siteName','logoPath','primaryColor','secondaryColor','textColor','bgColor'))

<script>
function changeQty(delta) {
    const inp = document.getElementById('qty');
    inp.value = Math.max(1, Math.min({{ $product->quantity }}, parseInt(inp.value) + delta));
}
function addToCart(id) {
    const qty = document.getElementById('qty').value;
    const btn = document.getElementById('addToCartBtn');
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Adding...';
    fetch('/cart/add/' + id, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': window._token, 'Content-Type': 'application/json' },
        body: JSON.stringify({ quantity: parseInt(qty) })
    }).then(r => r.json()).then(d => {
        btn.innerHTML = '<i class="fas fa-check mr-2"></i>Added!';
        document.querySelectorAll('.cart-count').forEach(el => {
            el.textContent = d.count ?? '';
            el.classList.toggle('hidden', !(d.count > 0));
        });
        setTimeout(() => { btn.innerHTML = '<i class="fas fa-shopping-cart mr-2"></i>Add to Cart'; }, 2000);
    }).catch(() => { btn.innerHTML = '<i class="fas fa-shopping-cart mr-2"></i>Add to Cart'; });
}
function toggleWishlist(id, btn) {
    fetch('/wishlist/toggle/' + id, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': window._token }
    }).then(r => r.json()).then(d => {
        btn.querySelector('i').className = d.added ? 'fas fa-heart text-red-500' : 'far fa-heart text-gray-400';
        document.querySelectorAll('.wishlist-count').forEach(el => {
            el.textContent = d.count ?? '';
            el.classList.toggle('hidden', !(d.count > 0));
        });
    });
}
function switchTab(tab) {
    ['description','specs','reviews'].forEach(t => {
        document.getElementById('panel-'+t).classList.toggle('hidden', t !== tab);
        document.getElementById('tab-'+t).classList.toggle('tab-active', t === tab);
        document.getElementById('tab-'+t).classList.toggle('border-transparent', t !== tab);
        document.getElementById('tab-'+t).classList.toggle('text-gray-500', t !== tab);
    });
}
</script>
</body>
</html>

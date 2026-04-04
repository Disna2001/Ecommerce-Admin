@php
    use App\Models\SiteSetting;
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

    $wishlistItems = $wishlistItems ?? collect();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Wishlist — {{ $siteName }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css','resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root{--c-primary:{{ $primaryColor }};--c-secondary:{{ $secondaryColor }};--c-text:{{ $textColor }};--c-bg:{{ $bgColor }};--c-nav:{{ $navBgColor }};}
        body{background:var(--c-bg);color:var(--c-text);}
        .btn-primary{background:var(--c-primary);color:#fff;}
        .btn-primary:hover{filter:brightness(.88);}
        .hover-primary:hover{color:var(--c-primary);}
        .card-hover{transition:all .25s;}
        .card-hover:hover{transform:translateY(-4px);box-shadow:0 20px 40px rgba(0,0,0,.08);}
        .removing{opacity:0;transform:scale(.95);transition:all .3s;}
        .heart-pulse{animation:heartbeat .4s ease;}
        @keyframes heartbeat{0%,100%{transform:scale(1)}50%{transform:scale(1.3)}}
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

<div class="container mx-auto px-4 py-8 max-w-6xl">

    <nav class="flex items-center gap-2 text-sm text-gray-400 mb-8">
        <a href="/" class="hover-primary">Home</a>
        <i class="fas fa-chevron-right text-xs"></i>
        <span class="text-gray-700 font-medium">My Wishlist</span>
    </nav>

    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 flex items-center gap-3">
                <i class="fas fa-heart text-red-500"></i> My Wishlist
            </h1>
            <p class="text-gray-400 mt-1">{{ $wishlistItems->count() }} saved {{ Str::plural('item',$wishlistItems->count()) }}</p>
        </div>
        @if($wishlistItems->isNotEmpty())
        <button onclick="addAllToCart()"
                class="btn-primary px-5 py-2.5 rounded-xl font-semibold text-sm flex items-center gap-2">
            <i class="fas fa-shopping-cart"></i> Add All to Cart
        </button>
        @endif
    </div>

    @if($wishlistItems->isEmpty())
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm py-24 text-center">
        <div class="w-24 h-24 mx-auto mb-6 rounded-full bg-red-50 flex items-center justify-center">
            <i class="fas fa-heart text-5xl text-red-300"></i>
        </div>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Your wishlist is empty</h2>
        <p class="text-gray-400 mb-8">Save items you love and come back to them later.</p>
        <a href="{{ url('/products') }}" class="btn-primary px-8 py-3 rounded-2xl font-bold inline-block">
            <i class="fas fa-search mr-2"></i>Discover Products
        </a>
    </div>
    @else
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5" id="wishlistGrid">
        @foreach($wishlistItems as $product)
        @php
            $discount = Discount::active()
                ->where(fn($q) => $q->where('scope','all')
                    ->orWhere(fn($q2) => $q2->where('scope','product')->where('scope_id',$product->id))
                    ->orWhere(fn($q2) => $q2->where('scope','category')->where('scope_id',$product->category_id)))
                ->orderByDesc('value')->first();
            $finalPrice = $discount ? max(0, $product->selling_price - $discount->calculateDiscount($product->selling_price)) : null;
        @endphp
        <div class="card-hover bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col" id="wish-{{ $product->id }}">
            <div class="relative h-48 bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center overflow-hidden">
                <a href="{{ url('/products/'.$product->id) }}">
                    @if(!empty($product->images) && count($product->images))
                        <img src="{{ Storage::url($product->images[0]) }}" alt="{{ $product->name }}"
                             class="w-full h-48 object-cover hover:scale-105 transition duration-300">
                    @else
                        <i class="fas fa-box text-5xl text-gray-200"></i>
                    @endif
                </a>
                {{-- Remove from wishlist --}}
                <button onclick="removeFromWishlist({{ $product->id }}, this)"
                        class="absolute top-3 right-3 w-9 h-9 bg-white rounded-full shadow-md flex items-center justify-center hover:bg-red-50 transition">
                    <i class="fas fa-heart text-red-500 text-sm"></i>
                </button>
                @if($discount)
                <span class="absolute top-3 left-3 bg-green-500 text-white text-xs font-bold px-2 py-1 rounded-lg">
                    {{ $discount->type==='percentage' ? '-'.$discount->value.'%' : '-Rs '.number_format($discount->value,0) }}
                </span>
                @endif
                @if($product->quantity <= 0)
                <div class="absolute inset-0 bg-black/40 flex items-center justify-center">
                    <span class="bg-white text-gray-900 text-xs font-bold px-3 py-1.5 rounded-full">Out of Stock</span>
                </div>
                @endif
            </div>

            <div class="p-4 flex flex-col flex-1">
                <p class="text-xs text-gray-400 mb-1">{{ $product->brand?->name ?? $product->category?->name ?? '' }}</p>
                <a href="{{ url('/products/'.$product->id) }}"
                   class="font-semibold text-gray-900 text-sm leading-snug mb-2 flex-1 hover-primary line-clamp-2">
                    {{ $product->name }}
                </a>

                <div class="flex items-center gap-1 mb-3">
                    @for($i=1;$i<=5;$i++)<i class="fas fa-star text-yellow-400 text-xs"></i>@endfor
                    <span class="text-xs text-gray-400 ml-1">(0)</span>
                </div>

                <div class="mb-3">
                    @if($finalPrice !== null)
                        <span class="font-bold text-gray-900">Rs {{ number_format($finalPrice,2) }}</span>
                        <span class="text-xs text-gray-400 line-through ml-1">Rs {{ number_format($product->selling_price,2) }}</span>
                    @else
                        <span class="font-bold text-gray-900">Rs {{ number_format($product->selling_price,2) }}</span>
                    @endif
                </div>

                <button onclick="addToCartFromWishlist({{ $product->id }}, this)"
                        class="w-full btn-primary py-2.5 rounded-xl font-semibold text-sm flex items-center justify-center gap-2 transition"
                        {{ $product->quantity <= 0 ? 'disabled style=opacity:.5;cursor:not-allowed' : '' }}>
                    <i class="fas fa-shopping-cart text-xs"></i>
                    {{ $product->quantity > 0 ? 'Add to Cart' : 'Out of Stock' }}
                </button>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

@include('frontend.partials.footer', compact('siteName','logoPath','primaryColor','secondaryColor','textColor','bgColor'))

<script>
const csrf = document.querySelector('meta[name=csrf-token]').content;

function removeFromWishlist(id, btn) {
    const card = document.getElementById('wish-'+id);
    fetch(`/wishlist/toggle/${id}`, {method:'POST', headers:{'X-CSRF-TOKEN':csrf}})
        .then(r=>r.json()).then(d => {
            card.classList.add('removing');
            setTimeout(() => {
                card.remove();
                document.querySelectorAll('.wishlist-count').forEach(el=>el.textContent=d.count??'');
                if(document.querySelectorAll('#wishlistGrid > div').length === 0) location.reload();
            }, 300);
        });
}

function addToCartFromWishlist(id, btn) {
    btn.innerHTML = '<i class="fas fa-spinner fa-spin text-xs"></i> Adding...';
    fetch(`/cart/add/${id}`, {
        method:'POST',
        headers:{'X-CSRF-TOKEN':csrf,'Content-Type':'application/json'},
        body: JSON.stringify({quantity:1})
    }).then(r=>r.json()).then(d=>{
        btn.innerHTML = '<i class="fas fa-check text-xs"></i> Added!';
        document.querySelectorAll('.cart-count').forEach(el=>el.textContent=d.count??'');
        setTimeout(()=>btn.innerHTML='<i class="fas fa-shopping-cart text-xs"></i> Add to Cart', 2000);
    });
}

function addAllToCart() {
    document.querySelectorAll('#wishlistGrid [id^="wish-"]').forEach(card => {
        const id = card.id.replace('wish-','');
        fetch(`/cart/add/${id}`, {
            method:'POST',
            headers:{'X-CSRF-TOKEN':csrf,'Content-Type':'application/json'},
            body: JSON.stringify({quantity:1})
        });
    });
    setTimeout(()=>{ window.location.href = '/cart'; }, 600);
}
</script>
</body>
</html>

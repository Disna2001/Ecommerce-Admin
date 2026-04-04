@php
    use App\Models\SiteSetting;
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

    // $cartItems and $cartSummary passed from controller
    // Fallback for direct view testing
    $cartItems   = $cartItems   ?? collect();
    $subtotal    = $subtotal    ?? 0;
    $discount    = $discount    ?? 0;
    $shipping    = $shipping    ?? 0;
    $total       = $total       ?? 0;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Cart — {{ $siteName }}</title>
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
        .qty-btn:hover{background:var(--c-primary);color:#fff;}
        .cart-row{transition:opacity .3s,transform .3s;}
        .cart-row.removing{opacity:0;transform:translateX(20px);}
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
        <span class="text-gray-700 font-medium">Shopping Cart</span>
    </nav>

    <h1 class="text-3xl font-extrabold text-gray-900 mb-8">
        Shopping Cart
        <span class="text-lg font-normal text-gray-400 ml-2">({{ $cartItems->count() }} {{ Str::plural('item',$cartItems->count()) }})</span>
    </h1>

    @if($cartItems->isEmpty())
    {{-- Empty Cart --}}
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm py-24 text-center">
        <div class="w-24 h-24 mx-auto mb-6 rounded-full flex items-center justify-center" style="background:{{ $primaryColor }}15">
            <i class="fas fa-shopping-cart text-4xl" style="color:{{ $primaryColor }}"></i>
        </div>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Your cart is empty</h2>
        <p class="text-gray-400 mb-8">Looks like you haven't added anything yet.</p>
        <a href="{{ url('/products') }}" class="btn-primary px-8 py-3 rounded-2xl font-bold inline-block">
            <i class="fas fa-arrow-left mr-2"></i>Continue Shopping
        </a>
    </div>
    @else

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- CART ITEMS --}}
        <div class="lg:col-span-2 space-y-4" id="cartContainer">
            @foreach($cartItems as $item)
            <div class="cart-row bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex gap-5" id="row-{{ $item['id'] }}">
                {{-- Image --}}
                <a href="{{ url('/products/'.$item['id']) }}" class="flex-shrink-0">
                    <div class="w-24 h-24 rounded-xl bg-gray-50 overflow-hidden flex items-center justify-center">
                        @if(!empty($item['image']))
                            <img src="{{ $item['image'] }}" class="w-full h-full object-cover">
                        @else
                            <i class="fas fa-box text-3xl text-gray-200"></i>
                        @endif
                    </div>
                </a>

                {{-- Details --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">{{ $item['brand'] ?? '' }}</p>
                            <a href="{{ url('/products/'.$item['id']) }}"
                               class="font-semibold text-gray-900 hover-primary leading-snug">{{ $item['name'] }}</a>
                            @if(!empty($item['variant']))
                            <p class="text-xs text-gray-400 mt-0.5">{{ $item['variant'] }}</p>
                            @endif
                        </div>
                        <button onclick="removeItem({{ $item['id'] }})"
                                class="text-gray-300 hover:text-red-500 transition flex-shrink-0">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <div class="flex items-center justify-between mt-4">
                        {{-- Qty --}}
                        <div class="flex items-center border border-gray-200 rounded-xl overflow-hidden">
                            <button onclick="updateQty({{ $item['id'] }}, -1)"
                                    class="qty-btn w-9 h-9 flex items-center justify-center text-gray-500 transition font-bold">−</button>
                            <input id="qty-{{ $item['id'] }}" type="number"
                                   value="{{ $item['quantity'] }}" min="1"
                                   onchange="setQty({{ $item['id'] }}, this.value)"
                                   class="w-12 h-9 text-center text-sm font-semibold border-0 focus:outline-none">
                            <button onclick="updateQty({{ $item['id'] }}, 1)"
                                    class="qty-btn w-9 h-9 flex items-center justify-center text-gray-500 transition font-bold">+</button>
                        </div>

                        {{-- Price --}}
                        <div class="text-right">
                            @if(!empty($item['original_price']) && $item['original_price'] > $item['price'])
                            <p class="text-xs text-gray-400 line-through">Rs {{ number_format($item['original_price'] * $item['quantity'],2) }}</p>
                            @endif
                            <p class="font-bold text-gray-900">Rs {{ number_format($item['price'] * $item['quantity'],2) }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach

            {{-- Coupon Code --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <h3 class="font-semibold text-gray-900 mb-3"><i class="fas fa-tag mr-2" style="color:{{ $primaryColor }}"></i>Coupon Code</h3>
                <form onsubmit="applyCoupon(event)" class="flex gap-3">
                    <input type="text" id="couponInput" placeholder="Enter coupon code"
                           class="flex-1 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-indigo-400 uppercase">
                    <button type="submit" class="btn-primary px-5 py-2.5 rounded-xl font-semibold text-sm">Apply</button>
                </form>
                <div id="couponMsg" class="mt-2 text-sm hidden"></div>
            </div>
        </div>

        {{-- ORDER SUMMARY --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sticky top-24">
                <h3 class="font-bold text-gray-900 text-lg mb-6">Order Summary</h3>

                <div class="space-y-3 mb-6">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Subtotal</span>
                        <span class="font-medium" id="subtotal">Rs {{ number_format($subtotal,2) }}</span>
                    </div>
                    @if($discount > 0)
                    <div class="flex justify-between text-sm">
                        <span class="text-green-600">Discount</span>
                        <span class="font-medium text-green-600">−Rs {{ number_format($discount,2) }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Shipping</span>
                        <span class="font-medium">{{ $shipping > 0 ? 'Rs '.number_format($shipping,2) : 'FREE' }}</span>
                    </div>
                    <div class="border-t border-gray-100 pt-3 flex justify-between">
                        <span class="font-bold text-gray-900">Total</span>
                        <span class="font-extrabold text-xl" style="color:{{ $primaryColor }}" id="orderTotal">
                            Rs {{ number_format($total,2) }}
                        </span>
                    </div>
                </div>

                <a href="{{ url('/checkout') }}"
                   class="w-full btn-primary py-3.5 rounded-2xl font-bold text-center block mb-3">
                    <i class="fas fa-lock mr-2"></i>Proceed to Checkout
                </a>
                <a href="{{ url('/products') }}"
                   class="w-full text-center block text-sm text-gray-400 hover-primary py-2 transition">
                    <i class="fas fa-arrow-left mr-1"></i>Continue Shopping
                </a>

                {{-- Trust Badges --}}
                <div class="mt-6 grid grid-cols-3 gap-3 text-center">
                    <div class="text-xs text-gray-400">
                        <i class="fas fa-shield-alt text-green-500 text-lg block mb-1"></i>Secure
                    </div>
                    <div class="text-xs text-gray-400">
                        <i class="fas fa-undo text-blue-500 text-lg block mb-1"></i>Returns
                    </div>
                    <div class="text-xs text-gray-400">
                        <i class="fas fa-truck text-purple-500 text-lg block mb-1"></i>Fast Ship
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@include('frontend.partials.footer', compact('siteName','logoPath','primaryColor','secondaryColor','textColor','bgColor'))

<script>
const csrf = document.querySelector('meta[name=csrf-token]').content;

function updateQty(id, delta) {
    const inp = document.getElementById('qty-'+id);
    const newVal = Math.max(1, parseInt(inp.value) + delta);
    inp.value = newVal;
    setQty(id, newVal);
}

function setQty(id, qty) {
    fetch(`/cart/update/${id}`, {
        method:'PATCH',
        headers:{'X-CSRF-TOKEN':csrf,'Content-Type':'application/json'},
        body: JSON.stringify({quantity: parseInt(qty)})
    }).then(r=>r.json()).then(d=>{
        if(d.subtotal) document.getElementById('subtotal').textContent = 'Rs '+d.subtotal;
        if(d.total)    document.getElementById('orderTotal').textContent = 'Rs '+d.total;
        document.querySelectorAll('.cart-count').forEach(el=>el.textContent=d.count??'');
    });
}

function removeItem(id) {
    const row = document.getElementById('row-'+id);
    row.classList.add('removing');
    setTimeout(() => {
        fetch(`/cart/remove/${id}`, {
            method:'DELETE',
            headers:{'X-CSRF-TOKEN':csrf}
        }).then(r=>r.json()).then(d=>{
            row.remove();
            if(d.subtotal) document.getElementById('subtotal').textContent = 'Rs '+d.subtotal;
            if(d.total)    document.getElementById('orderTotal').textContent = 'Rs '+d.total;
            document.querySelectorAll('.cart-count').forEach(el=>el.textContent=d.count??'');
            if(d.count === 0) location.reload();
        });
    }, 300);
}

function applyCoupon(e) {
    e.preventDefault();
    const code = document.getElementById('couponInput').value;
    const msg  = document.getElementById('couponMsg');
    fetch('/cart/coupon', {
        method:'POST',
        headers:{'X-CSRF-TOKEN':csrf,'Content-Type':'application/json'},
        body: JSON.stringify({code})
    }).then(r=>r.json()).then(d=>{
        msg.classList.remove('hidden','text-red-600','text-green-600');
        if(d.success) {
            msg.classList.add('text-green-600');
            msg.textContent = d.message;
            if(d.total) document.getElementById('orderTotal').textContent = 'Rs '+d.total;
        } else {
            msg.classList.add('text-red-600');
            msg.textContent = d.message ?? 'Invalid coupon code.';
        }
    });
}
</script>
</body>
</html>

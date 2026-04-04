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

    $cartItems = $cartItems ?? collect();
    $subtotal  = $subtotal  ?? 0;
    $discount  = $discount  ?? 0;
    $shipping  = $shipping  ?? 0;
    $total     = $total     ?? 0;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Checkout — {{ $siteName }}</title>
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
        .step-active{background:var(--c-primary);color:#fff;border-color:var(--c-primary);}
        .step-done{background:var(--c-primary);color:#fff;border-color:var(--c-primary);}
        .payment-option:has(input:checked){border-color:var(--c-primary);background:color-mix(in srgb,var(--c-primary) 5%,white);}
        input:focus,select:focus,textarea:focus{outline:none;border-color:var(--c-primary)!important;box-shadow:0 0 0 3px color-mix(in srgb,var(--c-primary) 15%,transparent);}
    </style>
</head>
<body class="font-sans antialiased">

{{-- Minimal nav for checkout --}}
<header class="shadow-sm" style="background:var(--c-nav)">
    <div class="container mx-auto px-4 h-16 flex items-center justify-between">
        <a href="/" class="text-xl font-extrabold" style="color:{{ $textColor }}">
            @if($logoPath)<img src="{{ Storage::url($logoPath) }}" class="h-8 object-contain">
            @else{{ $siteName }}@endif
        </a>
        <div class="flex items-center gap-2 text-sm text-gray-400">
            <i class="fas fa-lock text-green-500"></i> Secure Checkout
        </div>
    </div>
</header>

<div class="container mx-auto px-4 py-8 max-w-6xl">

    {{-- Step Indicator --}}
    <div class="flex items-center justify-center gap-0 mb-10">
        @foreach(['Cart' => 'fa-shopping-cart', 'Shipping' => 'fa-truck', 'Payment' => 'fa-credit-card', 'Confirm' => 'fa-check'] as $step => $icon)
        @php $stepN = array_search($step, array_keys(['Cart' => 0,'Shipping' => 0,'Payment' => 0,'Confirm' => 0])) + 1; @endphp
        <div class="flex items-center">
            <div class="flex flex-col items-center">
                <div class="w-10 h-10 rounded-full border-2 flex items-center justify-center font-bold text-sm
                            {{ $step === 'Shipping' ? 'step-active' : ($step === 'Cart' ? 'step-done' : 'border-gray-200 text-gray-300') }}">
                    <i class="fas {{ $icon }} text-sm"></i>
                </div>
                <span class="text-xs mt-1 {{ $step === 'Shipping' ? 'font-semibold' : 'text-gray-400' }}" style="{{ $step === 'Shipping' ? 'color:var(--c-primary)' : '' }}">{{ $step }}</span>
            </div>
            @if($step !== 'Confirm')
            <div class="w-16 h-px bg-gray-200 mx-2 mb-4"></div>
            @endif
        </div>
        @endforeach
    </div>

    @if(session('error'))
    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">{{ session('error') }}</div>
    @endif

    <form action="{{ url('/checkout/place-order') }}" method="POST" id="checkoutForm">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- LEFT: Shipping + Payment --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Shipping Info --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h2 class="font-bold text-gray-900 text-lg mb-5 flex items-center gap-2">
                        <i class="fas fa-map-marker-alt" style="color:{{ $primaryColor }}"></i>Shipping Information
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">First Name *</label>
                            <input type="text" name="first_name" value="{{ old('first_name', auth()->user()?->name ? explode(' ', auth()->user()->name)[0] : '') }}"
                                   required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm">
                            @error('first_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Last Name *</label>
                            <input type="text" name="last_name" value="{{ old('last_name') }}"
                                   required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email *</label>
                            <input type="email" name="email" value="{{ old('email', auth()->user()?->email) }}"
                                   required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm">
                            @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Phone *</label>
                            <input type="tel" name="phone" value="{{ old('phone') }}"
                                   required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm" placeholder="+94 XX XXX XXXX">
                            @error('phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Address *</label>
                            <input type="text" name="address" value="{{ old('address') }}"
                                   required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm" placeholder="Street address">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">City *</label>
                            <input type="text" name="city" value="{{ old('city') }}"
                                   required class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Postal Code</label>
                            <input type="text" name="postal_code" value="{{ old('postal_code') }}"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Order Notes (optional)</label>
                            <textarea name="notes" rows="2" placeholder="Special instructions..."
                                      class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm resize-none">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Payment Method --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h2 class="font-bold text-gray-900 text-lg mb-5 flex items-center gap-2">
                        <i class="fas fa-credit-card" style="color:{{ $primaryColor }}"></i>Payment Method
                    </h2>
                    <div class="space-y-3">
                        @foreach([
                            ['cod',  'fa-money-bill-wave', 'Cash on Delivery',   'Pay when your order arrives', 'text-green-600', 'bg-green-100'],
                            ['bank', 'fa-university',      'Bank Transfer',       'Transfer to our bank account', 'text-blue-600',  'bg-blue-100'],
                            ['card', 'fa-credit-card',     'Credit / Debit Card', 'Visa, Mastercard accepted',    'text-purple-600','bg-purple-100'],
                        ] as [$val, $icon, $label, $desc, $textClass, $bgClass])
                        <label class="payment-option flex items-center gap-4 p-4 border-2 border-gray-100 rounded-2xl cursor-pointer hover:border-indigo-200 transition">
                            <input type="radio" name="payment_method" value="{{ $val }}" {{ $val==='cod' ? 'checked' : '' }} class="sr-only">
                            <div class="w-10 h-10 rounded-xl {{ $bgClass }} flex items-center justify-center flex-shrink-0">
                                <i class="fas {{ $icon }} {{ $textClass }}"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-gray-900 text-sm">{{ $label }}</p>
                                <p class="text-xs text-gray-400">{{ $desc }}</p>
                            </div>
                            <div class="w-5 h-5 rounded-full border-2 border-gray-300 flex items-center justify-center payment-radio-indicator flex-shrink-0">
                                <div class="w-2.5 h-2.5 rounded-full hidden payment-dot" style="background:{{ $primaryColor }}"></div>
                            </div>
                        </label>
                        @endforeach
                    </div>

                    {{-- Card Details (shown when card selected) --}}
                    <div id="cardDetails" class="hidden mt-4 p-4 bg-gray-50 rounded-xl space-y-3">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Card Number</label>
                            <input type="text" placeholder="1234 5678 9012 3456" maxlength="19"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-mono">
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Expiry</label>
                                <input type="text" placeholder="MM/YY" maxlength="5"
                                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-mono">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">CVV</label>
                                <input type="text" placeholder="123" maxlength="4"
                                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-mono">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- RIGHT: Order Summary --}}
            <div>
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sticky top-8">
                    <h3 class="font-bold text-gray-900 text-lg mb-5">Order Summary</h3>

                    {{-- Items --}}
                    <div class="space-y-3 mb-5 max-h-64 overflow-y-auto">
                        @foreach($cartItems as $item)
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-lg bg-gray-50 flex items-center justify-center overflow-hidden flex-shrink-0">
                                @if(!empty($item['image']))
                                    <img src="{{ $item['image'] }}" class="w-full h-full object-cover">
                                @else
                                    <i class="fas fa-box text-gray-200"></i>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $item['name'] }}</p>
                                <p class="text-xs text-gray-400">Qty: {{ $item['quantity'] }}</p>
                            </div>
                            <p class="text-sm font-bold text-gray-900 flex-shrink-0">
                                Rs {{ number_format($item['price'] * $item['quantity'],2) }}
                            </p>
                        </div>
                        @endforeach
                    </div>

                    <div class="border-t border-gray-100 pt-4 space-y-2 mb-5">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Subtotal</span>
                            <span>Rs {{ number_format($subtotal,2) }}</span>
                        </div>
                        @if($discount > 0)
                        <div class="flex justify-between text-sm">
                            <span class="text-green-600">Discount</span>
                            <span class="text-green-600">−Rs {{ number_format($discount,2) }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Shipping</span>
                            <span>{{ $shipping > 0 ? 'Rs '.number_format($shipping,2) : 'FREE' }}</span>
                        </div>
                        <div class="flex justify-between pt-2 border-t border-gray-100 font-bold">
                            <span class="text-gray-900">Total</span>
                            <span class="text-xl" style="color:{{ $primaryColor }}">Rs {{ number_format($total,2) }}</span>
                        </div>
                    </div>

                    <button type="submit"
                            class="w-full btn-primary py-3.5 rounded-2xl font-bold text-base flex items-center justify-center gap-2">
                        <i class="fas fa-lock"></i> Place Order
                    </button>

                    <div class="flex items-center justify-center gap-4 mt-4 text-xs text-gray-400">
                        <span><i class="fas fa-shield-alt text-green-500 mr-1"></i>Secure</span>
                        <span><i class="fas fa-undo text-blue-500 mr-1"></i>Easy Returns</span>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
// Payment method toggle
document.querySelectorAll('.payment-option').forEach(option => {
    option.addEventListener('click', () => {
        document.querySelectorAll('.payment-option').forEach(o => {
            o.querySelector('.payment-dot').classList.add('hidden');
            o.querySelector('.payment-radio-indicator').classList.remove('border-indigo-500');
        });
        option.querySelector('.payment-dot').classList.remove('hidden');
        option.querySelector('.payment-radio-indicator').classList.add('border-indigo-500');
        option.querySelector('input').checked = true;
        document.getElementById('cardDetails').classList.toggle('hidden', option.querySelector('input').value !== 'card');
    });
    if(option.querySelector('input').checked) option.click();
});

// Card number formatting
document.querySelector('#cardDetails input[placeholder="1234 5678 9012 3456"]')?.addEventListener('input', e => {
    e.target.value = e.target.value.replace(/\D/g,'').replace(/(.{4})/g,'$1 ').trim();
});
</script>
</body>
</html>
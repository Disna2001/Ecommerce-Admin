@php
    use App\Models\Order;
    use App\Models\SiteSetting;
    use Illuminate\Support\Facades\Storage;
    $siteName     = SiteSetting::get('site_name',     'DISPLAY LANKA.LK');
    $logoPath     = SiteSetting::get('logo_path',     '');
    $primaryColor = SiteSetting::get('primary_color', '#4f46e5');
    $textColor    = SiteSetting::get('text_color',    '#111827');
    $navBgColor   = SiteSetting::get('nav_bg_color',  '#ffffff');
    $placedOrder  = Order::where('order_number', $order)->first();
    $needsVerification = $placedOrder?->needsPaymentVerification() ?? false;
    $isGatewayOrder = ($placedOrder?->payment_method === 'payhere');
    $isPaid = ($placedOrder?->payment_status === 'paid');
    $isCancelled = ($placedOrder?->status === 'cancelled');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order Placed — {{ $siteName }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css','resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root{--c-primary:{{ $primaryColor }};}
        body{background:#f9fafb;color:{{ $textColor }};}
        .btn-primary{background:var(--c-primary);color:#fff;}
        .btn-primary:hover{filter:brightness(.88);}
        .checkmark{animation:pop .5s cubic-bezier(.175,.885,.32,1.275) .2s both;}
        @keyframes pop{0%{transform:scale(0);opacity:0}100%{transform:scale(1);opacity:1}}
        .confetti-piece{position:absolute;width:8px;height:8px;border-radius:2px;animation:confettiFall 2s ease-in forwards;}
        @keyframes confettiFall{0%{transform:translateY(-20px) rotate(0deg);opacity:1}100%{transform:translateY(300px) rotate(720deg);opacity:0}}
    </style>
</head>
<body class="font-sans antialiased min-h-screen flex flex-col">

<header class="shadow-sm" style="background:{{ $navBgColor }}">
    <div class="container mx-auto px-4 h-16 flex items-center justify-between">
        <a href="/" class="text-xl font-extrabold" style="color:{{ $textColor }}">
            @if($logoPath)<img src="{{ Storage::url($logoPath) }}" class="h-8 object-contain">
            @else{{ $siteName }}@endif
        </a>
    </div>
</header>

<div class="flex-1 flex items-center justify-center p-8">
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-12 text-center max-w-md w-full relative overflow-hidden" id="successCard">

        {{-- Confetti --}}
        <div id="confetti" class="absolute inset-0 pointer-events-none overflow-hidden"></div>

        {{-- Check Icon --}}
        <div class="checkmark w-24 h-24 rounded-full mx-auto mb-6 flex items-center justify-center" style="background:{{ $primaryColor }}">
            <i class="fas fa-check text-white text-4xl"></i>
        </div>

        <h1 class="text-3xl font-extrabold text-gray-900 mb-2">{{ $isCancelled ? 'Payment Cancelled' : ($needsVerification ? 'Order Submitted!' : ($isPaid ? 'Payment Confirmed!' : 'Order Placed!')) }}</h1>
        <p class="text-gray-500 mb-2">{{ $isCancelled ? 'The order was cancelled because the payment was not completed.' : ($needsVerification ? 'Payment proof received and queued for review.' : ($isGatewayOrder ? ($isPaid ? 'Your online payment has been confirmed.' : 'We are waiting for the payment gateway confirmation.') : 'Thank you for your purchase.')) }}</p>
        <div class="inline-block px-4 py-2 rounded-xl text-sm font-bold mb-6" style="background:{{ $primaryColor }}15;color:{{ $primaryColor }}">
            Order #{{ $order }}
        </div>

        <p class="text-sm text-gray-400 mb-8 leading-relaxed">
            @if($isCancelled)
                The hosted payment was cancelled or did not complete, so the reserved stock was released again.<br>
                You can return to checkout and try the order again.
            @elseif($needsVerification)
                We received your order and payment submission. Our team will verify the proof and update the order once approved.<br>
                You'll receive a confirmation email when payment is reviewed.
            @elseif($isGatewayOrder && !$isPaid)
                PayHere returned your browser to the store, but the final paid state only updates after the server callback reaches this app.<br>
                On a local Herd URL, that callback needs a public domain or tunnel to complete automatically.
            @else
                We've received your order and will process it shortly.<br>
                You'll receive a confirmation email with tracking details.
            @endif
        </p>

        @if(session('gateway_notice'))
            <div class="mb-6 rounded-2xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-700">{{ session('gateway_notice') }}</div>
        @endif

        @if(session('gateway_error'))
            <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ session('gateway_error') }}</div>
        @endif

        <div class="grid grid-cols-3 gap-4 mb-8 text-center">
            <div>
                <div class="w-10 h-10 rounded-xl mx-auto mb-2 flex items-center justify-center bg-blue-50">
                    <i class="fas fa-envelope text-blue-500"></i>
                </div>
                <p class="text-xs text-gray-500">Confirmation sent</p>
            </div>
            <div>
                <div class="w-10 h-10 rounded-xl mx-auto mb-2 flex items-center justify-center bg-orange-50">
                    <i class="fas fa-box text-orange-500"></i>
                </div>
                <p class="text-xs text-gray-500">Processing order</p>
            </div>
            <div>
                <div class="w-10 h-10 rounded-xl mx-auto mb-2 flex items-center justify-center bg-green-50">
                    <i class="fas fa-truck text-green-500"></i>
                </div>
                <p class="text-xs text-gray-500">Ready to ship</p>
            </div>
        </div>

        <div class="flex gap-3">
            <a href="{{ url('/products') }}" class="flex-1 btn-primary py-3 rounded-2xl font-bold text-center">
                Continue Shopping
            </a>
            <a href="{{ url('/') }}" class="flex-1 py-3 rounded-2xl font-bold text-center border border-gray-200 text-gray-700 hover:bg-gray-50 transition">
                Go Home
            </a>
        </div>
    </div>
</div>

<script>
// Confetti burst
const colors = ['#4f46e5','#7c3aed','#06b6d4','#f59e0b','#10b981','#ef4444'];
const container = document.getElementById('confetti');
for(let i=0;i<40;i++){
    const el = document.createElement('div');
    el.className = 'confetti-piece';
    el.style.cssText = `
        left:${Math.random()*100}%;
        top:${-20+Math.random()*30}px;
        background:${colors[Math.floor(Math.random()*colors.length)]};
        animation-delay:${Math.random()*1}s;
        animation-duration:${1.5+Math.random()*1}s;
        width:${6+Math.random()*8}px;
        height:${6+Math.random()*8}px;
    `;
    container.appendChild(el);
}
</script>
</body>
</html>

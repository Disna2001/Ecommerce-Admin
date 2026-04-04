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

    $user        = auth()->user();
    $activeTab   = request('tab', 'overview');
    $cartCount   = collect(session('cart', []))->sum('quantity');
    $wishCount   = count(session('wishlist', []));

    // Orders — placeholder until Order model exists
    $orders = collect();
    if (class_exists(\App\Models\Order::class)) {
        $orders = \App\Models\Order::where('user_id', $user->id)
            ->latest()->get();
    }
    $pendingOrders  = $orders->whereIn('status', ['pending','processing']);
    $completedOrders = $orders->where('status', 'completed');
    $returnedOrders  = $orders->where('status', 'returned');

    // Wishlist products
    $wishlistIds = session('wishlist', []);
    $wishlistProducts = !empty($wishlistIds)
        ? \App\Models\Stock::with('brand')->whereIn('id', $wishlistIds)->where('status','active')->get()
        : collect();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>My Account — {{ $siteName }}</title>
    <script>window._token = '{{ csrf_token() }}';</script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css','resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --c-primary:   {{ $primaryColor }};
            --c-secondary: {{ $secondaryColor }};
            --c-text:      {{ $textColor }};
            --c-bg:        {{ $bgColor }};
            --c-nav:       {{ $navBgColor }};
        }
        body { background: var(--c-bg); color: var(--c-text); font-family: 'Inter', sans-serif; }
        .btn-primary { background: var(--c-primary); color: #fff; transition: filter .2s; }
        .btn-primary:hover { filter: brightness(.88); }
        .btn-outline { border: 2px solid var(--c-primary); color: var(--c-primary); transition: all .2s; }
        .btn-outline:hover { background: var(--c-primary); color: #fff; }
        .hover-primary:hover { color: var(--c-primary); }
        .nav-link.active { background: var(--c-primary); color: #fff !important; }
        .nav-link { transition: all .2s; }
        .nav-link:hover:not(.active) { background: color-mix(in srgb, var(--c-primary) 10%, transparent); color: var(--c-primary); }
        .avatar-ring { box-shadow: 0 0 0 4px white, 0 0 0 6px var(--c-primary); }
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--c-primary) !important;
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--c-primary) 15%, transparent);
        }
        .tab-panel { display: none; }
        .tab-panel.active { display: block; }
        .status-pending    { background: #fef3c7; color: #92400e; }
        .status-processing { background: #dbeafe; color: #1e40af; }
        .status-completed  { background: #dcfce7; color: #166534; }
        .status-cancelled  { background: #fee2e2; color: #991b1b; }
        .status-returned   { background: #f3f4f6; color: #374151; }
        .toggle-switch { position:relative; display:inline-block; width:44px; height:24px; }
        .toggle-switch input { opacity:0; width:0; height:0; }
        .toggle-slider { position:absolute; cursor:pointer; inset:0; background:#e5e7eb; border-radius:24px; transition:.3s; }
        .toggle-slider:before { content:''; position:absolute; height:18px; width:18px; left:3px; bottom:3px; background:white; border-radius:50%; transition:.3s; }
        input:checked + .toggle-slider { background: var(--c-primary); }
        input:checked + .toggle-slider:before { transform: translateX(20px); }
        .card { background: white; border-radius: 1rem; border: 1px solid #f1f5f9; box-shadow: 0 1px 3px rgba(0,0,0,.06); }
        .profile-hero { background: linear-gradient(135deg, var(--c-secondary) 0%, var(--c-primary) 100%); }
        @keyframes fadeIn { from{opacity:0;transform:translateY(8px)} to{opacity:1;transform:none} }
        .tab-panel.active { animation: fadeIn .3s ease; }
    </style>
</head>
<body class="antialiased">

{{-- TOP BAR --}}
@if($topbarEnabled)
<div class="py-2 text-center text-sm text-white font-medium"
     style="background:linear-gradient(to right,{{ $topbarFrom }},{{ $topbarTo }})">
    <i class="fas fa-tag mr-2"></i>{{ $topbarText }}
</div>
@endif

{{-- NAV --}}
<header class="shadow-sm sticky top-0 z-50" style="background:var(--c-nav)">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between h-16">
            <a href="/" class="flex items-center">
                @if($logoPath)
                    <img src="{{ Storage::url($logoPath) }}" class="h-9 object-contain">
                @else
                    <span class="text-xl font-extrabold" style="color:var(--c-text)">{{ $siteName }}</span>
                @endif
            </a>
            <div class="hidden md:flex items-center gap-1 text-sm">
                <a href="{{ url('/products') }}" class="px-3 py-2 text-gray-600 hover-primary rounded-lg hover:bg-gray-50">Products</a>
                <a href="{{ url('/products?sort=newest') }}" class="px-3 py-2 text-gray-600 hover-primary rounded-lg hover:bg-gray-50">New Arrivals</a>
                <a href="{{ url('/products?sort=price_asc') }}" class="px-3 py-2 text-gray-600 hover-primary rounded-lg hover:bg-gray-50">Deals</a>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ url('/wishlist') }}" class="relative p-2 text-gray-500 hover-primary">
                    <i class="fas fa-heart text-lg"></i>
                    @if($wishCount > 0)<span class="absolute -top-0.5 -right-0.5 bg-red-500 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center">{{ $wishCount }}</span>@endif
                </a>
                <a href="{{ url('/cart') }}" class="relative p-2 text-gray-500 hover-primary">
                    <i class="fas fa-shopping-bag text-lg"></i>
                    @if($cartCount > 0)<span class="absolute -top-0.5 -right-0.5 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center" style="background:var(--c-primary)">{{ $cartCount }}</span>@endif
                </a>
                {{-- Profile Dropdown --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center gap-2 pl-1 pr-3 py-1 rounded-full border border-gray-200 hover:border-indigo-300 transition">
                        <div class="w-8 h-8 rounded-full overflow-hidden flex items-center justify-center text-white text-sm font-bold flex-shrink-0"
                             style="background:var(--c-primary)">
                            @if($user->profile_photo_path ?? null)
                                <img src="{{ Storage::url($user->profile_photo_path) }}" class="w-full h-full object-cover">
                            @else
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            @endif
                        </div>
                        <span class="text-sm font-medium text-gray-700 hidden md:block">{{ explode(' ', $user->name)[0] }}</span>
                        <i class="fas fa-chevron-down text-xs text-gray-400"></i>
                    </button>
                    <div x-show="open" @click.away="open=false"
                         class="absolute right-0 mt-2 w-52 bg-white rounded-2xl shadow-xl border border-gray-100 z-50 overflow-hidden"
                         x-transition>
                        <div class="px-4 py-3 border-b border-gray-50">
                            <p class="text-sm font-semibold text-gray-900">{{ $user->name }}</p>
                            <p class="text-xs text-gray-400 truncate">{{ $user->email }}</p>
                        </div>
                        <div class="py-1">
                            @foreach([
                                ['overview',  'fa-user',         'My Profile'],
                                ['orders',    'fa-shopping-bag', 'My Orders'],
                                ['wishlist',  'fa-heart',        'Wishlist'],
                                ['settings',  'fa-cog',          'Settings'],
                                ['security',  'fa-shield-alt',   'Security'],
                            ] as [$tab, $icon, $label])
                            <a href="{{ url('/profile?tab='.$tab) }}"
                               class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 hover-primary transition">
                                <i class="fas {{ $icon }} w-4 text-gray-400"></i> {{ $label }}
                            </a>
                            @endforeach
                        </div>
                        <div class="border-t border-gray-100 py-1">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit"
                                        class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-500 hover:bg-red-50 transition">
                                    <i class="fas fa-sign-out-alt w-4"></i> Sign Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

{{-- SESSION FLASH --}}
@if(session('success'))
<div class="bg-green-50 border-b border-green-200 py-3 px-4 text-center text-sm text-green-700">
    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="bg-red-50 border-b border-red-200 py-3 px-4 text-center text-sm text-red-700">
    <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
</div>
@endif

{{-- PROFILE HERO --}}
<div class="profile-hero text-white py-10 px-4">
    <div class="container mx-auto max-w-5xl flex flex-col md:flex-row items-center md:items-end gap-6">
        {{-- Avatar --}}
        <div class="relative flex-shrink-0">
            <div class="w-24 h-24 rounded-full overflow-hidden avatar-ring flex items-center justify-center text-3xl font-black text-white"
                 style="background: rgba(255,255,255,.25)">
                @if($user->profile_photo_path ?? null)
                    <img src="{{ Storage::url($user->profile_photo_path) }}" class="w-full h-full object-cover">
                @else
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                @endif
            </div>
            <a href="{{ url('/profile?tab=settings') }}"
               class="absolute bottom-0 right-0 w-7 h-7 bg-white rounded-full flex items-center justify-center shadow"
               title="Change photo">
                <i class="fas fa-camera text-xs" style="color:var(--c-primary)"></i>
            </a>
        </div>
        {{-- Info --}}
        <div class="text-center md:text-left flex-1">
            <h1 class="text-2xl md:text-3xl font-extrabold">{{ $user->name }}</h1>
            <p class="text-white/70 text-sm mt-1">{{ $user->email }}</p>
            <div class="flex flex-wrap gap-4 mt-3 justify-center md:justify-start text-sm">
                <span class="flex items-center gap-1.5 bg-white/20 px-3 py-1 rounded-full">
                    <i class="fas fa-shopping-bag"></i> {{ $orders->count() }} Orders
                </span>
                <span class="flex items-center gap-1.5 bg-white/20 px-3 py-1 rounded-full">
                    <i class="fas fa-heart"></i> {{ $wishCount }} Saved
                </span>
                <span class="flex items-center gap-1.5 bg-white/20 px-3 py-1 rounded-full">
                    <i class="fas fa-calendar"></i> Member since {{ $user->created_at->format('M Y') }}
                </span>
            </div>
        </div>
        {{-- Quick actions --}}
        <div class="flex gap-2">
            <a href="{{ url('/cart') }}"
               class="flex items-center gap-2 bg-white/20 hover:bg-white/30 transition px-4 py-2 rounded-xl text-sm font-semibold">
                <i class="fas fa-shopping-cart"></i> Cart ({{ $cartCount }})
            </a>
        </div>
    </div>
</div>

{{-- MAIN LAYOUT --}}
<div class="container mx-auto max-w-5xl px-4 py-8">
    <div class="flex flex-col lg:flex-row gap-6">

        {{-- SIDEBAR NAV --}}
        <aside class="lg:w-56 flex-shrink-0">
            <nav class="card p-2 space-y-0.5 sticky top-24">
                @php
                $navItems = [
                    ['overview',  'fa-th-large',      'Overview'],
                    ['orders',    'fa-shopping-bag',   'My Orders'],
                    ['pending',   'fa-clock',          'Pending Orders'],
                    ['returns',   'fa-undo',           'Returns'],
                    ['wishlist',  'fa-heart',          'Wishlist'],
                    ['addresses', 'fa-map-marker-alt', 'Address Book'],
                    ['cards',     'fa-credit-card',    'Payment Cards'],
                    ['settings',  'fa-user-edit',      'Profile Settings'],
                    ['security',  'fa-shield-alt',     'Security'],
                    ['connected', 'fa-link',           'Connected Accounts'],
                ];
                @endphp
                @foreach($navItems as [$tab, $icon, $label])
                <a href="#" onclick="switchTab('{{ $tab }}'); return false;"
                   class="nav-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-600 {{ $activeTab === $tab ? 'active' : '' }}"
                   id="nav-{{ $tab }}">
                    <i class="fas {{ $icon }} w-4 text-center opacity-70"></i>
                    {{ $label }}
                    @if($tab === 'pending' && $pendingOrders->count() > 0)
                        <span class="ml-auto text-xs font-bold bg-orange-100 text-orange-600 px-1.5 py-0.5 rounded-full">
                            {{ $pendingOrders->count() }}
                        </span>
                    @endif
                </a>
                @endforeach
                {{-- Logout --}}
                <form action="{{ route('logout') }}" method="POST" class="pt-2 border-t border-gray-100 mt-2">
                    @csrf
                    <button type="submit"
                            class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-red-500 hover:bg-red-50 transition">
                        <i class="fas fa-sign-out-alt w-4 text-center"></i> Sign Out
                    </button>
                </form>
            </nav>
        </aside>

        {{-- CONTENT --}}
        <main class="flex-1 min-w-0">

            {{-- ═══ OVERVIEW ═══ --}}
            <div id="panel-overview" class="tab-panel {{ $activeTab === 'overview' ? 'active' : '' }}">
                <h2 class="text-xl font-bold text-gray-900 mb-5">Account Overview</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    @foreach([
                        ['fa-shopping-bag',   $orders->count(),          'Total Orders',   'indigo'],
                        ['fa-clock',          $pendingOrders->count(),   'Pending',        'orange'],
                        ['fa-check-circle',   $completedOrders->count(), 'Completed',      'green'],
                        ['fa-heart',          $wishCount,                'Wishlist',       'red'],
                    ] as [$icon, $count, $label, $color])
                    <div class="card p-5 text-center">
                        <div class="w-12 h-12 rounded-2xl mx-auto mb-3 flex items-center justify-center bg-{{ $color }}-100">
                            <i class="fas {{ $icon }} text-xl text-{{ $color }}-500"></i>
                        </div>
                        <p class="text-2xl font-extrabold text-gray-900">{{ $count }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $label }}</p>
                    </div>
                    @endforeach
                </div>

                {{-- Quick links --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    @foreach([
                        ['/cart',     'fa-shopping-cart', 'Shopping Cart',   'View cart ('.$cartCount.' items)',        'indigo'],
                        ['/wishlist', 'fa-heart',         'Wishlist',        'View saved items ('.$wishCount.')',       'red'],
                        ['/products', 'fa-search',        'Browse Products', 'Discover new items',                     'purple'],
                        ['?tab=settings', 'fa-user-edit', 'Edit Profile',   'Update your info and photo',              'green'],
                    ] as [$href, $icon, $title, $desc, $color])
                    <a href="{{ url('/profile'.$href) }}"
                       class="card p-4 flex items-center gap-4 hover:shadow-md transition group">
                        <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0 bg-{{ $color }}-100 group-hover:bg-{{ $color }}-200 transition">
                            <i class="fas {{ $icon }} text-{{ $color }}-600"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 text-sm">{{ $title }}</p>
                            <p class="text-xs text-gray-400">{{ $desc }}</p>
                        </div>
                        <i class="fas fa-chevron-right text-gray-300 ml-auto text-xs group-hover:translate-x-1 transition"></i>
                    </a>
                    @endforeach
                </div>

                {{-- Recent orders --}}
                @if($orders->isNotEmpty())
                <div class="card overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-50 flex items-center justify-between">
                        <h3 class="font-bold text-gray-900">Recent Orders</h3>
                        <button onclick="switchTab('orders')" class="text-xs hover-primary font-medium" style="color:var(--c-primary)">View all →</button>
                    </div>
                    @foreach($orders->take(3) as $order)
                    <div class="px-5 py-4 border-b border-gray-50 last:border-0 flex items-center justify-between">
                        <div>
                            <p class="font-semibold text-sm text-gray-900">#{{ $order->order_number ?? $order->id }}</p>
                            <p class="text-xs text-gray-400">{{ $order->created_at->format('M d, Y') }}</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-sm font-bold text-gray-900">Rs {{ number_format($order->total ?? 0, 2) }}</span>
                            <span class="text-xs px-2 py-1 rounded-full font-semibold status-{{ $order->status }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="card p-10 text-center text-gray-400">
                    <i class="fas fa-shopping-bag text-4xl mb-3 block opacity-30"></i>
                    <p class="font-medium">No orders yet</p>
                    <a href="{{ url('/products') }}" class="btn-primary inline-block mt-4 px-6 py-2 rounded-xl text-sm font-semibold">Start Shopping</a>
                </div>
                @endif
            </div>

            {{-- ═══ ALL ORDERS ═══ --}}
            <div id="panel-orders" class="tab-panel {{ $activeTab === 'orders' ? 'active' : '' }}">
                <h2 class="text-xl font-bold text-gray-900 mb-5">My Orders</h2>
                @include('profile.partials.orders-list', ['orders' => $orders, 'emptyMessage' => 'You have no orders yet.'])
            </div>

            {{-- ═══ PENDING ORDERS ═══ --}}
            <div id="panel-pending" class="tab-panel {{ $activeTab === 'pending' ? 'active' : '' }}">
                <h2 class="text-xl font-bold text-gray-900 mb-5">Pending Orders</h2>
                @include('profile.partials.orders-list', ['orders' => $pendingOrders, 'emptyMessage' => 'No pending orders.'])
            </div>

            {{-- ═══ RETURNS ═══ --}}
            <div id="panel-returns" class="tab-panel {{ $activeTab === 'returns' ? 'active' : '' }}">
                <h2 class="text-xl font-bold text-gray-900 mb-5">Returns & Refunds</h2>
                @if($returnedOrders->isNotEmpty())
                    @include('profile.partials.orders-list', ['orders' => $returnedOrders, 'emptyMessage' => ''])
                @else
                <div class="card p-12 text-center text-gray-400">
                    <i class="fas fa-undo text-4xl mb-3 block opacity-30"></i>
                    <p class="font-medium text-gray-600">No returns or refunds</p>
                    <p class="text-sm mt-1">Items you return will appear here</p>
                </div>
                @endif
                <div class="card p-5 mt-4 border-l-4" style="border-left-color:var(--c-primary)">
                    <p class="font-semibold text-gray-900 mb-1"><i class="fas fa-info-circle mr-2" style="color:var(--c-primary)"></i>Return Policy</p>
                    <p class="text-sm text-gray-500">Items can be returned within 30 days of delivery. Contact support with your order number to start a return.</p>
                </div>
            </div>

            {{-- ═══ WISHLIST ═══ --}}
            <div id="panel-wishlist" class="tab-panel {{ $activeTab === 'wishlist' ? 'active' : '' }}">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-xl font-bold text-gray-900">Wishlist <span class="text-gray-400 font-normal text-base">({{ $wishlistProducts->count() }})</span></h2>
                    @if($wishlistProducts->isNotEmpty())
                    <a href="{{ url('/wishlist') }}" class="btn-outline px-4 py-2 rounded-xl text-sm font-semibold">View Full Page</a>
                    @endif
                </div>
                @if($wishlistProducts->isNotEmpty())
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach($wishlistProducts as $p)
                    <a href="{{ url('/products/'.$p->id) }}" class="card overflow-hidden group hover:shadow-md transition">
                        <div class="h-36 bg-gray-50 flex items-center justify-center overflow-hidden">
                            @if(!empty($p->images) && count($p->images))
                                <img src="{{ Storage::url($p->images[0]) }}" class="w-full h-36 object-cover group-hover:scale-105 transition">
                            @else
                                <i class="fas fa-box text-3xl text-gray-200"></i>
                            @endif
                        </div>
                        <div class="p-3">
                            <p class="text-xs text-gray-400 mb-0.5">{{ $p->brand?->name ?? '' }}</p>
                            <p class="font-semibold text-sm text-gray-900 truncate">{{ $p->name }}</p>
                            <p class="font-bold text-gray-900 mt-1">Rs {{ number_format($p->selling_price, 2) }}</p>
                        </div>
                    </a>
                    @endforeach
                </div>
                @else
                <div class="card p-12 text-center text-gray-400">
                    <i class="fas fa-heart text-4xl mb-3 block opacity-30"></i>
                    <p class="font-medium text-gray-600">Your wishlist is empty</p>
                    <a href="{{ url('/products') }}" class="btn-primary inline-block mt-4 px-6 py-2 rounded-xl text-sm font-semibold">Discover Products</a>
                </div>
                @endif
            </div>

            {{-- ═══ ADDRESS BOOK ═══ --}}
            <div id="panel-addresses" class="tab-panel {{ $activeTab === 'addresses' ? 'active' : '' }}">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-xl font-bold text-gray-900">Address Book</h2>
                    <button onclick="document.getElementById('addAddressForm').classList.toggle('hidden')"
                            class="btn-primary px-4 py-2 rounded-xl text-sm font-semibold flex items-center gap-2">
                        <i class="fas fa-plus"></i> Add Address
                    </button>
                </div>

                {{-- Add Address Form --}}
                <div id="addAddressForm" class="hidden card p-6 mb-5">
                    <h3 class="font-bold text-gray-900 mb-4">New Address</h3>
                    <form action="{{ url('/profile/addresses') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Full Name *</label>
                                <input type="text" name="name" value="{{ $user->name }}" required
                                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Phone *</label>
                                <input type="tel" name="phone" required
                                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Address Line *</label>
                                <input type="text" name="address" required
                                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">City *</label>
                                <input type="text" name="city" required
                                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Postal Code</label>
                                <input type="text" name="postal_code"
                                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm">
                            </div>
                            <div class="md:col-span-2">
                                <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
                                    <input type="checkbox" name="is_default" class="rounded" value="1"> Set as default address
                                </label>
                            </div>
                        </div>
                        <div class="flex gap-2 mt-4">
                            <button type="submit" class="btn-primary px-6 py-2.5 rounded-xl font-semibold text-sm">Save Address</button>
                            <button type="button"
                                    onclick="document.getElementById('addAddressForm').classList.add('hidden')"
                                    class="px-6 py-2.5 rounded-xl font-semibold text-sm border border-gray-200 text-gray-600 hover:bg-gray-50">Cancel</button>
                        </div>
                    </form>
                </div>

                {{-- Address list placeholder --}}
                @php $addresses = auth()->user()->addresses ?? collect(); @endphp
                @if($addresses->isNotEmpty())
                <div class="space-y-3">
                    @foreach($addresses as $addr)
                    <div class="card p-5 flex items-start justify-between">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 bg-indigo-100">
                                <i class="fas fa-map-marker-alt text-indigo-600"></i>
                            </div>
                            <div>
                                <div class="flex items-center gap-2 mb-0.5">
                                    <p class="font-semibold text-gray-900 text-sm">{{ $addr->name }}</p>
                                    @if($addr->is_default)<span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-semibold">Default</span>@endif
                                </div>
                                <p class="text-sm text-gray-500">{{ $addr->address }}, {{ $addr->city }} {{ $addr->postal_code }}</p>
                                <p class="text-sm text-gray-400">{{ $addr->phone }}</p>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button class="text-indigo-600 hover:text-indigo-800 text-sm px-2 py-1 rounded-lg hover:bg-indigo-50 transition">Edit</button>
                            <button class="text-red-500 hover:text-red-700 text-sm px-2 py-1 rounded-lg hover:bg-red-50 transition">Delete</button>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="card p-12 text-center text-gray-400">
                    <i class="fas fa-map-marker-alt text-4xl mb-3 block opacity-30"></i>
                    <p class="font-medium text-gray-600">No saved addresses</p>
                    <p class="text-sm mt-1">Add an address for faster checkout</p>
                </div>
                @endif
            </div>

            {{-- ═══ PAYMENT CARDS ═══ --}}
            <div id="panel-cards" class="tab-panel {{ $activeTab === 'cards' ? 'active' : '' }}">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-xl font-bold text-gray-900">Payment Cards</h2>
                    <button onclick="document.getElementById('addCardForm').classList.toggle('hidden')"
                            class="btn-primary px-4 py-2 rounded-xl text-sm font-semibold flex items-center gap-2">
                        <i class="fas fa-plus"></i> Add Card
                    </button>
                </div>

                {{-- Add Card Form --}}
                <div id="addCardForm" class="hidden card p-6 mb-5">
                    <h3 class="font-bold text-gray-900 mb-4">Add New Card</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Card Number</label>
                            <input type="text" id="cardNum" placeholder="1234 5678 9012 3456" maxlength="19"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-mono">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Cardholder Name</label>
                            <input type="text" placeholder="Name on card"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Expiry Date</label>
                            <input type="text" placeholder="MM/YY" maxlength="5"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-mono">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">CVV</label>
                            <input type="text" placeholder="•••" maxlength="4"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-mono">
                        </div>
                    </div>
                    <div class="flex items-center gap-3 mt-4 text-xs text-gray-400">
                        <i class="fas fa-lock text-green-500"></i> Your card details are encrypted and secure
                    </div>
                    <div class="flex gap-2 mt-4">
                        <button class="btn-primary px-6 py-2.5 rounded-xl font-semibold text-sm">Save Card</button>
                        <button onclick="document.getElementById('addCardForm').classList.add('hidden')"
                                class="px-6 py-2.5 rounded-xl font-semibold text-sm border border-gray-200 text-gray-600 hover:bg-gray-50">Cancel</button>
                    </div>
                </div>

                <div class="card p-12 text-center text-gray-400">
                    <i class="fas fa-credit-card text-4xl mb-3 block opacity-30"></i>
                    <p class="font-medium text-gray-600">No saved cards</p>
                    <p class="text-sm mt-1">Save a card for faster checkout</p>
                </div>
            </div>

            {{-- ═══ PROFILE SETTINGS ═══ --}}
            <div id="panel-settings" class="tab-panel {{ $activeTab === 'settings' ? 'active' : '' }}">
                <h2 class="text-xl font-bold text-gray-900 mb-5">Profile Settings</h2>

                <form action="{{ url('/profile/update') }}" method="POST" enctype="multipart/form-data">
                    @csrf @method('PATCH')

                    {{-- Profile Photo --}}
                    <div class="card p-6 mb-4">
                        <h3 class="font-bold text-gray-900 mb-4">Profile Photo</h3>
                        <div class="flex items-center gap-5">
                            <div class="w-20 h-20 rounded-full overflow-hidden flex items-center justify-center text-2xl font-black text-white flex-shrink-0"
                                 style="background:var(--c-primary)" id="avatarPreview">
                                @if($user->profile_photo_path ?? null)
                                    <img src="{{ Storage::url($user->profile_photo_path) }}" class="w-full h-full object-cover" id="avatarImg">
                                @else
                                    <span id="avatarInitial">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                @endif
                            </div>
                            <div>
                                <label class="btn-outline px-4 py-2 rounded-xl text-sm font-semibold cursor-pointer inline-block">
                                    <i class="fas fa-upload mr-2"></i>Upload Photo
                                    <input type="file" name="profile_photo" accept="image/*" class="hidden"
                                           onchange="previewPhoto(this)">
                                </label>
                                <p class="text-xs text-gray-400 mt-2">JPG, PNG or GIF. Max 2MB.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Personal Info --}}
                    <div class="card p-6 mb-4">
                        <h3 class="font-bold text-gray-900 mb-4">Personal Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Full Name *</label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm">
                                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email Address *</label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm">
                                @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Phone Number</label>
                                <input type="tel" name="phone" value="{{ old('phone', $user->phone ?? '') }}"
                                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Date of Birth</label>
                                <input type="date" name="dob" value="{{ old('dob', $user->dob ?? '') }}"
                                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Default Address</label>
                                <textarea name="address" rows="2"
                                          class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm resize-none"
                                          placeholder="Your delivery address...">{{ old('address', $user->address ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Preferences --}}
                    <div class="card p-6 mb-5">
                        <h3 class="font-bold text-gray-900 mb-4">Notification Preferences</h3>
                        <div class="space-y-4">
                            @foreach([
                                ['email_offers',  $email_offers,  'Email Promotions',    'Receive deals and offers via email'],
                                ['sms_alerts',    $sms_alerts,    'SMS Alerts',          'Get order updates via SMS'],
                                ['order_updates', $order_updates, 'Order Notifications', 'Status change alerts'],
                            ] as [$prop, $val, $label, $desc])
                            <div class="flex items-center justify-between py-1">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $label }}</p>
                                    <p class="text-xs text-gray-400">{{ $desc }}</p>
                                </div>
                                <button wire:click="$toggle('{{ $prop }}')"
                                        class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors duration-200"
                                        style="{{ $val ? 'background:var(--c-primary)' : 'background:#e5e7eb' }}">
                                    <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform duration-200"
                                        style="{{ $val ? 'transform:translateX(20px)' : 'transform:translateX(4px)' }}"></span>
                                </button>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <button type="submit" class="btn-primary px-8 py-3 rounded-2xl font-bold text-sm flex items-center gap-2">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                        <button type="reset" class="px-8 py-3 rounded-2xl font-bold text-sm border border-gray-200 text-gray-600 hover:bg-gray-50 transition">
                            Reset
                        </button>
                    </div>
                </form>
            </div>

            {{-- ═══ SECURITY ═══ --}}
            <div id="panel-security" class="tab-panel {{ $activeTab === 'security' ? 'active' : '' }}">
                <h2 class="text-xl font-bold text-gray-900 mb-5">Security Settings</h2>

                {{-- Change Password --}}
                <div class="card p-6 mb-4">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center">
                            <i class="fas fa-lock text-indigo-600"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900">Change Password</h3>
                            <p class="text-xs text-gray-400">Use a strong password with 8+ characters</p>
                        </div>
                    </div>
                    <form action="{{ url('/profile/password') }}" method="POST">
                        @csrf @method('PATCH')
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Current Password *</label>
                                <div class="relative">
                                    <input type="password" name="current_password" id="currentPw" required
                                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm pr-10">
                                    <button type="button" onclick="togglePw('currentPw',this)"
                                            class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600">
                                        <i class="fas fa-eye text-sm"></i>
                                    </button>
                                </div>
                                @error('current_password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">New Password *</label>
                                <div class="relative">
                                    <input type="password" name="password" id="newPw" required
                                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm pr-10"
                                           oninput="checkPwStrength(this.value)">
                                    <button type="button" onclick="togglePw('newPw',this)"
                                            class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600">
                                        <i class="fas fa-eye text-sm"></i>
                                    </button>
                                </div>
                                {{-- Strength bar --}}
                                <div class="mt-2 flex gap-1">
                                    @for($i=0;$i<4;$i++)
                                    <div class="h-1 flex-1 rounded-full bg-gray-200 pw-strength-bar" id="pwBar{{ $i }}"></div>
                                    @endfor
                                </div>
                                <p class="text-xs text-gray-400 mt-1" id="pwStrengthLabel">Enter a password</p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Confirm New Password *</label>
                                <input type="password" name="password_confirmation" required
                                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm">
                                @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>
                        <button type="submit" class="mt-5 btn-primary px-6 py-2.5 rounded-xl font-semibold text-sm">
                            Update Password
                        </button>
                    </form>
                </div>

                {{-- Two-Step Verification --}}
                <div class="card p-6 mb-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center">
                                <i class="fas fa-shield-alt text-green-600"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900">Two-Step Verification</h3>
                                <p class="text-xs text-gray-400">Add an extra layer of security to your account</p>
                            </div>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" id="twoFactorToggle" onchange="toggle2FA(this)"
                                   {{ ($user->two_factor_secret ?? null) ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                    <div id="twoFactorSetup" class="{{ ($user->two_factor_secret ?? null) ? '' : 'hidden' }} mt-5 p-4 bg-green-50 rounded-xl border border-green-100">
                        <p class="text-sm text-green-700 font-semibold mb-2">
                            <i class="fas fa-check-circle mr-2"></i>Two-step verification is enabled
                        </p>
                        <p class="text-xs text-green-600">Your account is protected with an authenticator app or SMS code.</p>
                        <button onclick="show2FASetup()" class="mt-3 text-xs font-semibold underline text-green-700">Manage settings</button>
                    </div>
                </div>

                {{-- Active Sessions --}}
                <div class="card p-6 mb-4">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-orange-100 flex items-center justify-center">
                            <i class="fas fa-desktop text-orange-600"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900">Active Sessions</h3>
                            <p class="text-xs text-gray-400">Devices currently logged in to your account</p>
                        </div>
                    </div>
                    <div class="flex items-center justify-between py-3 border-b border-gray-50">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-laptop text-gray-400 text-lg"></i>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">Current Device</p>
                                <p class="text-xs text-gray-400">{{ request()->ip() }} — {{ now()->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                        <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full font-semibold">Active</span>
                    </div>
                    <form action="{{ url('/profile/logout-other-devices') }}" method="POST" class="mt-4">
                        @csrf
                        <div class="flex items-center gap-3">
                            <input type="password" name="password" placeholder="Confirm password to sign out other devices"
                                   class="flex-1 border border-gray-200 rounded-xl px-4 py-2 text-sm">
                            <button type="submit" class="px-4 py-2 rounded-xl text-sm font-semibold text-red-600 border border-red-200 hover:bg-red-50 transition">
                                Sign Out Others
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Danger Zone --}}
                <div class="card p-6 border border-red-100">
                    <h3 class="font-bold text-red-600 mb-3 flex items-center gap-2">
                        <i class="fas fa-exclamation-triangle"></i> Danger Zone
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">These actions are irreversible. Please proceed with caution.</p>
                    <button onclick="if(confirm('Are you sure you want to delete your account? This cannot be undone.')) document.getElementById('deleteAccountForm').submit()"
                            class="px-5 py-2.5 rounded-xl text-sm font-semibold text-red-600 border border-red-200 hover:bg-red-50 transition">
                        <i class="fas fa-trash mr-2"></i>Delete Account
                    </button>
                    <form id="deleteAccountForm" action="{{ url('/profile/delete') }}" method="POST" class="hidden">
                        @csrf @method('DELETE')
                    </form>
                </div>
            </div>

            {{-- ═══ CONNECTED ACCOUNTS ═══ --}}
                @if($activeTab === 'connected')
                <h2 class="text-xl font-bold text-gray-900 mb-2">Connected Accounts</h2>
                <p class="text-sm text-gray-500 mb-6">
                    Link social accounts for one-click sign-in. Your email and password still work regardless.
                </p>

                <div class="space-y-4">

                    {{-- ── GOOGLE ─────────────────────────────── --}}
                    @php $googleConnected = !empty($user->google_id); @endphp
                    <div class="card p-5">
                        <div class="flex items-center justify-between gap-4 flex-wrap">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl flex items-center justify-center flex-shrink-0 bg-white border border-gray-100 shadow-sm">
                                    <svg viewBox="0 0 24 24" width="26" height="26">
                                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 mb-0.5">
                                        <p class="font-semibold text-gray-900">Google</p>
                                        @if($googleConnected)
                                        <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-semibold flex items-center gap-1">
                                            <i class="fas fa-check text-xs"></i> Connected
                                        </span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-400">
                                        {{ $googleConnected ? 'Signed in with ' . $user->email : 'Use your Google account to sign in' }}
                                    </p>
                                </div>
                            </div>
                            @if($googleConnected)
                            <button wire:click="disconnectSocial('google')"
                                    wire:confirm="Disconnect your Google account?"
                                    class="px-4 py-2 rounded-xl text-sm font-semibold text-red-500 border border-red-200 hover:bg-red-50 transition flex items-center gap-2">
                                <i class="fas fa-unlink text-xs"></i> Disconnect
                            </button>
                            @else
                            <a href="{{ url('/auth/google') }}"
                            class="px-4 py-2 rounded-xl text-sm font-semibold text-white flex items-center gap-2 transition hover:opacity-90"
                            style="background:#4285F4">
                                <i class="fab fa-google text-xs"></i> Connect Google
                            </a>
                            @endif
                        </div>
                    </div>

                    {{-- ── FACEBOOK ────────────────────────────── --}}
                    @php $fbConnected = !empty($user->facebook_id); @endphp
                    <div class="card p-5">
                        <div class="flex items-center justify-between gap-4 flex-wrap">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl flex items-center justify-center flex-shrink-0" style="background:#1877F2">
                                    <i class="fab fa-facebook-f text-white text-xl"></i>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 mb-0.5">
                                        <p class="font-semibold text-gray-900">Facebook</p>
                                        @if($fbConnected)
                                        <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-semibold flex items-center gap-1">
                                            <i class="fas fa-check text-xs"></i> Connected
                                        </span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-400">
                                        {{ $fbConnected ? 'Facebook account linked' : 'Use your Facebook account to sign in' }}
                                    </p>
                                </div>
                            </div>
                            @if($fbConnected)
                            <button wire:click="disconnectSocial('facebook')"
                                    wire:confirm="Disconnect your Facebook account?"
                                    class="px-4 py-2 rounded-xl text-sm font-semibold text-red-500 border border-red-200 hover:bg-red-50 transition flex items-center gap-2">
                                <i class="fas fa-unlink text-xs"></i> Disconnect
                            </button>
                            @else
                            <a href="{{ url('/auth/facebook') }}"
                            class="px-4 py-2 rounded-xl text-sm font-semibold text-white flex items-center gap-2 transition hover:opacity-90"
                            style="background:#1877F2">
                                <i class="fab fa-facebook-f text-xs"></i> Connect Facebook
                            </a>
                            @endif
                        </div>
                    </div>

                    {{-- ── GITHUB ──────────────────────────────── --}}
                    @php $ghConnected = !empty($user->github_id); @endphp
                    <div class="card p-5">
                        <div class="flex items-center justify-between gap-4 flex-wrap">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl flex items-center justify-center flex-shrink-0 bg-gray-900">
                                    <i class="fab fa-github text-white text-xl"></i>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 mb-0.5">
                                        <p class="font-semibold text-gray-900">GitHub</p>
                                        @if($ghConnected)
                                        <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-semibold flex items-center gap-1">
                                            <i class="fas fa-check text-xs"></i> Connected
                                        </span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-400">
                                        {{ $ghConnected ? 'GitHub account linked' : 'Use your GitHub account to sign in' }}
                                    </p>
                                </div>
                            </div>
                            @if($ghConnected)
                            <button wire:click="disconnectSocial('github')"
                                    wire:confirm="Disconnect your GitHub account?"
                                    class="px-4 py-2 rounded-xl text-sm font-semibold text-red-500 border border-red-200 hover:bg-red-50 transition flex items-center gap-2">
                                <i class="fas fa-unlink text-xs"></i> Disconnect
                            </button>
                            @else
                            <a href="{{ url('/auth/github') }}"
                            class="px-4 py-2 rounded-xl text-sm font-semibold text-white flex items-center gap-2 transition hover:opacity-90"
                            style="background:#24292e">
                                <i class="fab fa-github text-xs"></i> Connect GitHub
                            </a>
                            @endif
                        </div>
                    </div>

                    {{-- ── Security note ───────────────────────── --}}
                    <div class="card p-4 border border-indigo-100 bg-indigo-50">
                        <p class="text-sm text-indigo-700 flex items-start gap-2">
                            <i class="fas fa-shield-alt mt-0.5 flex-shrink-0"></i>
                            <span>
                                Connecting a social account lets you sign in faster. We never post on your behalf.
                                You can disconnect at any time as long as you have another way to sign in.
                            </span>
                        </p>
                    </div>

                </div>
                @endif

        </main>
    </div>
</div>

{{-- Footer --}}
@include('frontend.partials.footer', compact('siteName','logoPath','primaryColor','secondaryColor','textColor','bgColor'))

<script>
// ── Tab switching ─────────────────────────────────────────────
const TABS = ['overview','orders','pending','returns','wishlist','addresses','cards','settings','security','connected'];

function switchTab(tab) {
    TABS.forEach(t => {
        const panel = document.getElementById('panel-' + t);
        const nav   = document.getElementById('nav-'   + t);
        if (panel) panel.classList.toggle('active', t === tab);
        if (nav)   nav.classList.toggle('active',   t === tab);
    });
    history.replaceState(null, '', '/profile?tab=' + tab);
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// ── Password show/hide ────────────────────────────────────────
function togglePw(id, btn) {
    const inp = document.getElementById(id);
    const icon = btn.querySelector('i');
    if (inp.type === 'password') {
        inp.type = 'text';
        icon.className = 'fas fa-eye-slash text-sm';
    } else {
        inp.type = 'password';
        icon.className = 'fas fa-eye text-sm';
    }
}

// ── Password strength ─────────────────────────────────────────
function checkPwStrength(pw) {
    const bars   = document.querySelectorAll('.pw-strength-bar');
    const label  = document.getElementById('pwStrengthLabel');
    const colors = ['#ef4444','#f97316','#eab308','#22c55e'];
    const labels = ['Too weak','Weak','Good','Strong'];

    let score = 0;
    if (pw.length >= 8)            score++;
    if (/[A-Z]/.test(pw))          score++;
    if (/[0-9]/.test(pw))          score++;
    if (/[^A-Za-z0-9]/.test(pw))   score++;

    bars.forEach((b, i) => {
        b.style.background = i < score ? colors[score - 1] : '#e5e7eb';
    });
    label.textContent  = pw ? labels[score - 1] || 'Too weak' : 'Enter a password';
    label.style.color  = pw ? colors[score - 1] : '#9ca3af';
}

// ── Photo preview ─────────────────────────────────────────────
function previewPhoto(input) {
    if (!input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
        const preview = document.getElementById('avatarPreview');
        preview.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover rounded-full">`;
    };
    reader.readAsDataURL(input.files[0]);
}

// ── Card number formatting ────────────────────────────────────
document.getElementById('cardNum')?.addEventListener('input', e => {
    e.target.value = e.target.value.replace(/\D/g, '').replace(/(.{4})/g, '$1 ').trim().slice(0, 19);
});

// ── 2FA toggle ────────────────────────────────────────────────
function toggle2FA(checkbox) {
    const setup = document.getElementById('twoFactorSetup');
    setup.classList.toggle('hidden', !checkbox.checked);
}

// ── Init: activate correct tab from URL ──────────────────────
const urlTab = new URLSearchParams(location.search).get('tab') || 'overview';
switchTab(urlTab);
</script>
</body>
</html>

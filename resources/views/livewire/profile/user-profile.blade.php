<div>
{{-- ── TOAST ────────────────────────────────────────────────── --}}
<div x-data="{ show:false, message:'', type:'success' }"
     x-on:notify.window="show=true; message=$event.detail.message; type=$event.detail.type; setTimeout(()=>show=false,3500)"
     x-show="show" x-transition
     class="fixed bottom-5 right-5 z-50 px-5 py-3 rounded-2xl shadow-xl text-sm font-semibold text-white flex items-center gap-2"
     :class="type==='success'?'bg-green-500':(type==='error'?'bg-red-500':'bg-indigo-500')"
     style="display:none">
    <i class="fas" :class="type==='success'?'fa-check-circle':type==='error'?'fa-times-circle':'fa-info-circle'"></i>
    <span x-text="message"></span>
</div>

{{-- ── HERO ─────────────────────────────────────────────────── --}}
<div class="text-white py-10 px-4" style="background:linear-gradient(135deg,var(--c-secondary),var(--c-primary))">
    <div class="container mx-auto max-w-5xl flex flex-col md:flex-row items-center md:items-end gap-6">

        {{-- Avatar with upload overlay --}}
        <div class="relative flex-shrink-0 group" x-data="{ hovering: false }">
            <div class="w-24 h-24 rounded-full overflow-hidden flex items-center justify-center text-3xl font-black text-white"
                 style="box-shadow:0 0 0 4px white,0 0 0 6px var(--c-primary);background:rgba(255,255,255,.2)">
                @if($profile_photo)
                    <img src="{{ $profile_photo->temporaryUrl() }}" class="w-full h-full object-cover">
                @elseif($user->profile_photo_path ?? null)
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($user->profile_photo_path) }}"
                         class="w-full h-full object-cover" wire:key="avatar-{{ $user->updated_at }}">
                @else
                    <span>{{ strtoupper(substr($user->name,0,1)) }}</span>
                @endif
            </div>

            {{-- Upload trigger --}}
            <label class="absolute inset-0 rounded-full flex items-center justify-center cursor-pointer
                           bg-black/0 hover:bg-black/40 transition-all duration-200 group-hover:bg-black/40">
                <div class="opacity-0 group-hover:opacity-100 transition flex flex-col items-center">
                    <i class="fas fa-camera text-white text-xl"></i>
                    <span class="text-white text-xs mt-1">Change</span>
                </div>
                <input type="file" wire:model="profile_photo" accept="image/*" class="hidden">
            </label>
        </div>

        {{-- Save/cancel photo buttons (shown after selecting) --}}
        <div class="text-center md:text-left flex-1">
            <h1 class="text-2xl md:text-3xl font-extrabold">{{ $user->name }}</h1>
            <p class="text-white/70 text-sm mt-1">{{ $user->email }}</p>

            @if($profile_photo)
            <div class="flex items-center gap-2 mt-3">
                <span class="text-white/80 text-xs">New photo selected —</span>
                <button wire:click="savePhoto"
                        wire:loading.attr="disabled"
                        class="bg-white text-xs font-bold px-3 py-1.5 rounded-lg transition hover:bg-gray-100"
                        style="color:var(--c-primary)">
                    <span wire:loading.remove wire:target="savePhoto"><i class="fas fa-check mr-1"></i>Save Photo</span>
                    <span wire:loading wire:target="savePhoto"><i class="fas fa-spinner fa-spin mr-1"></i>Saving...</span>
                </button>
                <button wire:click="$set('profile_photo', null)"
                        class="text-white/70 hover:text-white text-xs px-2 py-1.5 rounded-lg border border-white/30 hover:border-white transition">
                    Cancel
                </button>
            </div>
            @else
            <div class="flex flex-wrap gap-3 mt-3 justify-center md:justify-start text-sm">
                <span class="bg-white/20 px-3 py-1 rounded-full text-xs flex items-center gap-1.5">
                    <i class="fas fa-shopping-bag"></i> {{ $orders->count() }} Orders
                </span>
                <span class="bg-white/20 px-3 py-1 rounded-full text-xs flex items-center gap-1.5">
                    <i class="fas fa-heart"></i> {{ $wishlistProducts->count() }} Saved
                </span>
                <span class="bg-white/20 px-3 py-1 rounded-full text-xs flex items-center gap-1.5">
                    <i class="fas fa-star"></i> {{ $reviews->count() }} Reviews
                </span>
                <span class="bg-white/20 px-3 py-1 rounded-full text-xs flex items-center gap-1.5">
                    <i class="fas fa-calendar"></i> Since {{ $user->created_at->format('M Y') }}
                </span>
            </div>
            @endif
        </div>

        <a href="{{ url('/cart') }}"
           class="bg-white/20 hover:bg-white/30 transition px-4 py-2 rounded-xl text-sm font-semibold flex items-center gap-2">
            <i class="fas fa-shopping-cart"></i>
            Cart ({{ collect(session('cart', []))->sum('quantity') }})
        </a>
    </div>
</div>

{{-- ── BODY ─────────────────────────────────────────────────── --}}
<div class="container mx-auto max-w-5xl px-4 py-8">
    <div class="flex flex-col lg:flex-row gap-6">

        {{-- SIDEBAR NAV --}}
        <aside class="lg:w-52 flex-shrink-0">
            <nav class="card p-2 space-y-0.5 sticky top-24">
                @php
                $navItems = [
                    ['overview',  'fa-th-large',        'Overview'],
                    ['orders',    'fa-shopping-bag',     'My Orders'],
                    ['pending',   'fa-clock',            'Pending'],
                    ['returns',   'fa-undo',             'Returns'],
                    ['reviews',   'fa-star',             'My Reviews'],
                    ['wishlist',  'fa-heart',            'Wishlist'],
                    ['addresses', 'fa-map-marker-alt',   'Addresses'],
                    ['cards',     'fa-credit-card',      'Cards'],
                    ['settings',  'fa-user-edit',        'Settings'],
                    ['security',  'fa-shield-alt',       'Security'],
                    ['connected', 'fa-link',             'Connected'],
                ];
                @endphp
                @foreach($navItems as [$tab, $icon, $label])
                <button wire:click="setTab('{{ $tab }}')"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition text-left"
                        style="{{ $activeTab===$tab ? 'background:var(--c-primary);color:#fff' : 'color:#4b5563' }}">
                    <i class="fas {{ $icon }} w-4 text-center opacity-70 flex-shrink-0 text-xs"></i>
                    {{ $label }}
                    @if($tab==='pending' && $orders->whereIn('status',['pending','processing'])->count()>0)
                    <span class="ml-auto text-xs font-bold bg-orange-100 text-orange-600 px-1.5 py-0.5 rounded-full">
                        {{ $orders->whereIn('status',['pending','processing'])->count() }}
                    </span>
                    @endif
                    @if($tab==='reviews' && $unreviewedProducts->count()>0)
                    <span class="ml-auto text-xs font-bold bg-yellow-100 text-yellow-700 px-1.5 py-0.5 rounded-full">
                        {{ $unreviewedProducts->count() }} new
                    </span>
                    @endif
                </button>
                @endforeach
                <div class="pt-2 border-t border-gray-100 mt-2">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-red-500 hover:bg-red-50 transition">
                            <i class="fas fa-sign-out-alt w-4 text-center text-xs"></i> Sign Out
                        </button>
                    </form>
                </div>
            </nav>
        </aside>

        {{-- MAIN CONTENT --}}
        <main class="flex-1 min-w-0">

            {{-- ════ OVERVIEW ════ --}}
            @if($activeTab === 'overview')
            <h2 class="text-xl font-bold text-gray-900 mb-5">Account Overview</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                @foreach([
                    ['fa-shopping-bag', $orders->count(),                                          'Total Orders', 'indigo'],
                    ['fa-clock',        $orders->whereIn('status',['pending','processing'])->count(),'Pending',     'orange'],
                    ['fa-check-circle', $orders->where('status','completed')->count(),             'Completed',    'green'],
                    ['fa-star',         $reviews->count(),                                         'Reviews',      'yellow'],
                ] as [$icon,$count,$label,$color])
                <div class="card p-5 text-center">
                    <div class="w-12 h-12 rounded-2xl mx-auto mb-3 flex items-center justify-center bg-{{ $color }}-100">
                        <i class="fas {{ $icon }} text-{{ $color }}-500 text-xl"></i>
                    </div>
                    <p class="text-2xl font-extrabold text-gray-900">{{ $count }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $label }}</p>
                </div>
                @endforeach
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                @foreach([
                    ['/cart',            'fa-shopping-cart', 'Cart',         'View cart ('.collect(session('cart',[]))->sum('quantity').' items)', 'indigo'],
                    ['/wishlist',        'fa-heart',         'Wishlist',     'Saved items ('.$wishlistProducts->count().')',                       'red'],
                    ['?tab=reviews',     'fa-star',          'My Reviews',   $reviews->count().' review(s) written',                               'yellow'],
                    ['?tab=settings',    'fa-user-edit',     'Edit Profile', 'Update your info & photo',                                           'green'],
                ] as [$href,$icon,$title,$desc,$color])
                <a href="{{ \Illuminate\Support\Str::startsWith($href,'/') ? url($href) : url('/profile'.$href) }}"
                   class="card p-4 flex items-center gap-4 hover:shadow-md transition group">
                    <div class="w-10 h-10 rounded-xl bg-{{ $color }}-100 group-hover:bg-{{ $color }}-200 transition flex items-center justify-center flex-shrink-0">
                        <i class="fas {{ $icon }} text-{{ $color }}-600"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-gray-900 text-sm">{{ $title }}</p>
                        <p class="text-xs text-gray-400">{{ $desc }}</p>
                    </div>
                    <i class="fas fa-chevron-right text-gray-300 text-xs group-hover:translate-x-1 transition"></i>
                </a>
                @endforeach
            </div>
            @if($orders->isNotEmpty())
            <div class="card overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-50 flex items-center justify-between">
                    <h3 class="font-bold text-gray-900 text-sm">Recent Orders</h3>
                    <button wire:click="setTab('orders')" class="text-xs font-semibold" style="color:var(--c-primary)">View all →</button>
                </div>
                @foreach($orders->take(3) as $order)
                <div class="px-5 py-3.5 border-b border-gray-50 last:border-0 flex items-center justify-between">
                    <div>
                        <p class="font-semibold text-sm text-gray-900">#{{ $order->order_number ?? $order->id }}</p>
                        <p class="text-xs text-gray-400">{{ $order->created_at->format('M d, Y') }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="font-bold text-sm text-gray-900">Rs {{ number_format($order->total ?? 0, 2) }}</span>
                        @php
                            $sc = ['pending'=>['#fef3c7','#92400e'],'processing'=>['#dbeafe','#1e40af'],'completed'=>['#dcfce7','#166534'],'cancelled'=>['#fee2e2','#991b1b']];
                            $c  = $sc[$order->status ?? 'pending'] ?? ['#f3f4f6','#374151'];
                        @endphp
                        <span class="text-xs px-2 py-1 rounded-full font-semibold"
                              style="background:{{ $c[0] }};color:{{ $c[1] }}">
                            {{ ucfirst($order->status ?? 'pending') }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="card p-10 text-center text-gray-400">
                <i class="fas fa-shopping-bag text-4xl block mb-3 opacity-30"></i>
                <p class="font-medium text-gray-600">No orders yet</p>
                <a href="{{ url('/products') }}" class="btn-primary inline-block mt-4 px-6 py-2 rounded-xl text-sm font-semibold">Start Shopping</a>
            </div>
            @endif
            @endif

            {{-- ════ ORDERS / PENDING / RETURNS ════ --}}
            @if(in_array($activeTab, ['orders','pending','returns']))
            @php
                $filteredOrders = match($activeTab) {
                    'pending' => $orders->whereIn('status', ['pending','processing']),
                    'returns' => $orders->whereIn('status', ['return_requested','return_approved','returned','refunded']),
                    default   => $orders,
                };
                $tabTitle = ['orders'=>'My Orders','pending'=>'Pending Orders','returns'=>'Returns & Refunds'][$activeTab];
                $scMap    = ['pending'=>['#fef3c7','#92400e'],'processing'=>['#dbeafe','#1e40af'],'shipped'=>['#cffafe','#0e7490'],'completed'=>['#dcfce7','#166534'],'cancelled'=>['#fee2e2','#991b1b'],'return_requested'=>['#ffedd5','#9a3412'],'returned'=>['#f3f4f6','#374151'],'refunded'=>['#ede9fe','#5b21b6']];
            @endphp
            <h2 class="text-xl font-bold text-gray-900 mb-5">{{ $tabTitle }}</h2>
            @if($filteredOrders->isNotEmpty())
            <div class="space-y-3">
                @foreach($filteredOrders as $order)
                <div class="card overflow-hidden" x-data="{ open: false }">
                    <div class="px-5 py-4 bg-gray-50 border-b border-gray-100 flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-5 text-sm flex-wrap">
                            <div>
                                <p class="text-xs text-gray-400 uppercase font-semibold">Order</p>
                                <p class="font-bold text-gray-900">#{{ $order->order_number ?? $order->id }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 uppercase font-semibold">Date</p>
                                <p class="font-medium text-gray-700">{{ $order->created_at->format('M d, Y') }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 uppercase font-semibold">Total</p>
                                <p class="font-bold text-gray-900">Rs {{ number_format($order->total ?? 0, 2) }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 uppercase font-semibold">Payment</p>
                                <p class="font-medium text-gray-700">{{ ucfirst($order->payment_method ?? 'cod') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            @php $c2 = $scMap[$order->status ?? 'pending'] ?? ['#f3f4f6','#374151']; @endphp
                            <span class="text-xs px-3 py-1.5 rounded-full font-bold"
                                  style="background:{{ $c2[0] }};color:{{ $c2[1] }}">
                                {{ ucfirst(str_replace('_', ' ', $order->status ?? 'Pending')) }}
                            </span>
                            <button @click="open=!open"
                                    class="text-xs font-semibold px-3 py-1.5 rounded-lg border border-gray-200 hover:bg-gray-50 transition">
                                Details <i class="fas fa-chevron-down ml-1 text-xs" :class="open?'rotate-180':''"></i>
                            </button>
                        </div>
                    </div>
                    <div x-show="open" x-transition>
                        {{-- Order items --}}
                        @if($order->items && $order->items->isNotEmpty())
                        <div class="divide-y divide-gray-50">
                            @foreach($order->items as $item)
                            <div class="px-5 py-3 flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0 overflow-hidden">
                                    @if($item->stock && !empty($item->stock->images))
                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($item->stock->images[0]) }}" class="w-full h-full object-cover">
                                    @else
                                        <i class="fas fa-box text-gray-300 text-xs"></i>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $item->product_name }}</p>
                                    <p class="text-xs text-gray-400">Qty {{ $item->quantity }} × Rs {{ number_format($item->sale_price, 2) }}</p>
                                </div>
                                <p class="text-sm font-bold text-gray-900">Rs {{ number_format($item->subtotal, 2) }}</p>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="px-5 py-3 text-sm text-gray-400">No item details available.</div>
                        @endif

                        {{-- Actions --}}
                        <div class="px-5 py-3 bg-gray-50 border-t border-gray-100 flex flex-wrap gap-2">
                            @if(in_array($order->status ?? '', ['completed','delivered']))
                            <button wire:click="openNewReview({{ $order->items->first()?->stock_id ?? 0 }}); setTab('reviews')"
                                    class="text-xs font-semibold text-yellow-700 border border-yellow-200 px-3 py-1.5 rounded-lg hover:bg-yellow-50 transition">
                                <i class="fas fa-star mr-1"></i>Write Review
                            </button>
                            @endif
                            @if($order->tracking_number ?? null)
                            <a href="{{ $order->tracking_url ?? '#' }}" target="_blank"
                               class="text-xs font-semibold text-cyan-600 border border-cyan-200 px-3 py-1.5 rounded-lg hover:bg-cyan-50 transition">
                                <i class="fas fa-truck mr-1"></i>Track: {{ $order->tracking_number }}
                            </a>
                            @endif
                            <button class="text-xs font-semibold text-gray-600 border border-gray-200 px-3 py-1.5 rounded-lg hover:bg-gray-50 transition">
                                <i class="fas fa-download mr-1"></i>Invoice
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="card p-12 text-center text-gray-400">
                <i class="fas fa-shopping-bag text-4xl block mb-3 opacity-30"></i>
                <p class="font-medium text-gray-600">No {{ strtolower($tabTitle) }}</p>
                <a href="{{ url('/products') }}" class="btn-primary inline-block mt-4 px-6 py-2 rounded-xl text-sm font-semibold">Browse Products</a>
            </div>
            @endif
            @endif

            {{-- ════ REVIEWS ════ --}}
            @if($activeTab === 'reviews')
            <div class="flex items-center justify-between mb-5 flex-wrap gap-3">
                <h2 class="text-xl font-bold text-gray-900">My Reviews</h2>
                <div class="flex items-center gap-2">
                    <select wire:model.live="reviewFilter"
                            class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none bg-white">
                        <option value="all">All Reviews</option>
                        <option value="approved">Approved</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>
            </div>

            {{-- Write / Edit Review Form --}}
            @if($showReviewForm)
            <div class="card p-6 mb-5 border-2" style="border-color:var(--c-primary)">
                <h3 class="font-bold text-gray-900 mb-4">
                    {{ $editingReviewId ? 'Edit Review' : 'Write a Review' }}
                </h3>

                {{-- Star rating picker --}}
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-gray-600 mb-2 uppercase tracking-wide">Rating *</label>
                    <div class="flex items-center gap-2">
                        @for($i = 1; $i <= 5; $i++)
                        <button type="button" wire:click="$set('review_rating', {{ $i }})"
                                class="text-3xl transition hover:scale-110"
                                style="color: {{ $review_rating >= $i ? '#f59e0b' : '#d1d5db' }}">
                            ★
                        </button>
                        @endfor
                        <span class="text-sm text-gray-500 ml-2">
                            {{ ['','Poor','Fair','Good','Very Good','Excellent'][$review_rating] ?? '' }}
                        </span>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Review Title</label>
                    <input wire:model="review_title" type="text" placeholder="Summarise your experience..."
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-indigo-400">
                    @error('review_title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Review *</label>
                    <textarea wire:model="review_body" rows="4"
                              placeholder="Share your thoughts about the product (min 10 characters)..."
                              class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm resize-none focus:outline-none focus:border-indigo-400"></textarea>
                    <p class="text-xs text-gray-400 mt-1 text-right">{{ strlen($review_body) }}/2000</p>
                    @error('review_body')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="flex gap-2">
                    <button wire:click="saveReview" wire:loading.attr="disabled"
                            class="btn-primary px-6 py-2.5 rounded-xl font-semibold text-sm flex items-center gap-2">
                        <span wire:loading.remove wire:target="saveReview">
                            <i class="fas fa-paper-plane mr-1"></i>{{ $editingReviewId ? 'Update' : 'Submit' }} Review
                        </span>
                        <span wire:loading wire:target="saveReview">
                            <i class="fas fa-spinner fa-spin mr-1"></i>Saving...
                        </span>
                    </button>
                    <button wire:click="cancelReview"
                            class="px-6 py-2.5 rounded-xl font-semibold text-sm border border-gray-200 text-gray-600 hover:bg-gray-50 transition">
                        Cancel
                    </button>
                </div>
            </div>
            @endif

            {{-- Products awaiting review --}}
            @if($unreviewedProducts->isNotEmpty() && !$showReviewForm)
            <div class="card p-5 mb-5 border border-yellow-200 bg-yellow-50">
                <p class="font-semibold text-yellow-800 mb-3 flex items-center gap-2 text-sm">
                    <i class="fas fa-star text-yellow-500"></i>
                    {{ $unreviewedProducts->count() }} product{{ $unreviewedProducts->count() > 1 ? 's' : '' }} awaiting your review
                </p>
                <div class="flex flex-wrap gap-2">
                    @foreach($unreviewedProducts as $up)
                    <button wire:click="openNewReview({{ $up->id }})"
                            class="flex items-center gap-2 bg-white border border-yellow-200 px-3 py-2 rounded-xl text-sm font-medium text-gray-700 hover:border-yellow-400 transition">
                        @if(!empty($up->images))
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($up->images[0]) }}" class="w-6 h-6 rounded object-cover">
                        @endif
                        {{ \Illuminate\Support\Str::limit($up->name, 30) }}
                        <span class="text-yellow-600 text-xs font-bold">+ Review</span>
                    </button>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Reviews list --}}
            @if($reviews->isNotEmpty())
            <div class="space-y-4">
                @foreach($reviews as $review)
                <div class="card p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-start gap-4 flex-1 min-w-0">
                            {{-- Product image --}}
                            <a href="{{ url('/products/'.$review->stock_id) }}"
                               class="flex-shrink-0 w-16 h-16 rounded-xl overflow-hidden bg-gray-100 flex items-center justify-center">
                                @if($review->stock && !empty($review->stock->images))
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($review->stock->images[0]) }}"
                                         class="w-full h-full object-cover">
                                @else
                                    <i class="fas fa-box text-gray-300"></i>
                                @endif
                            </a>
                            <div class="flex-1 min-w-0">
                                {{-- Stars --}}
                                <div class="flex items-center gap-1 mb-1">
                                    @for($i=1; $i<=5; $i++)
                                    <i class="fas fa-star text-xs {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-200' }}"></i>
                                    @endfor
                                    <span class="text-xs text-gray-400 ml-1">{{ $review->created_at->diffForHumans() }}</span>
                                    @if(!$review->is_approved)
                                    <span class="ml-2 text-xs bg-orange-100 text-orange-600 px-2 py-0.5 rounded-full font-semibold">Pending</span>
                                    @else
                                    <span class="ml-2 text-xs bg-green-100 text-green-600 px-2 py-0.5 rounded-full font-semibold">Published</span>
                                    @endif
                                </div>
                                {{-- Product name --}}
                                <a href="{{ url('/products/'.$review->stock_id) }}"
                                   class="text-xs text-indigo-500 hover:underline truncate block mb-1">
                                    {{ $review->stock?->name ?? 'Product' }}
                                </a>
                                {{-- Title + body --}}
                                @if($review->title)
                                <p class="font-semibold text-gray-900 text-sm">{{ $review->title }}</p>
                                @endif
                                <p class="text-sm text-gray-600 leading-relaxed mt-0.5 line-clamp-3">{{ $review->body }}</p>
                            </div>
                        </div>
                        {{-- Actions --}}
                        <div class="flex flex-col gap-1.5 flex-shrink-0">
                            <button wire:click="editReview({{ $review->id }})"
                                    class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 flex items-center justify-center transition"
                                    title="Edit">
                                <i class="fas fa-edit text-xs"></i>
                            </button>
                            <button wire:click="deleteReview({{ $review->id }})"
                                    wire:confirm="Delete this review? This cannot be undone."
                                    class="w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-100 flex items-center justify-center transition"
                                    title="Delete">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @elseif(!$showReviewForm)
            <div class="card p-12 text-center text-gray-400">
                <i class="fas fa-star text-4xl block mb-3 opacity-20"></i>
                <p class="font-medium text-gray-600">No reviews yet</p>
                <p class="text-sm mt-1">Purchase and review products to share your experience</p>
                <a href="{{ url('/products') }}"
                   class="btn-primary inline-block mt-4 px-6 py-2 rounded-xl text-sm font-semibold">Browse Products</a>
            </div>
            @endif
            @endif

            {{-- ════ WISHLIST ════ --}}
            @if($activeTab === 'wishlist')
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-xl font-bold text-gray-900">
                    Wishlist <span class="text-gray-400 font-normal text-base">({{ $wishlistProducts->count() }})</span>
                </h2>
            </div>
            @if($wishlistProducts->isNotEmpty())
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                @foreach($wishlistProducts as $p)
                <a href="{{ url('/products/'.$p->id) }}" class="card overflow-hidden group hover:shadow-md transition">
                    <div class="h-36 bg-gray-50 flex items-center justify-center overflow-hidden">
                        @if(!empty($p->images))
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($p->images[0]) }}" class="w-full h-36 object-cover group-hover:scale-105 transition">
                        @else
                            <i class="fas fa-box text-3xl text-gray-200"></i>
                        @endif
                    </div>
                    <div class="p-3">
                        <p class="text-xs text-gray-400">{{ $p->brand?->name ?? '' }}</p>
                        <p class="font-semibold text-sm text-gray-900 truncate">{{ $p->name }}</p>
                        <p class="font-bold text-gray-900 mt-1 text-sm">Rs {{ number_format($p->selling_price, 2) }}</p>
                    </div>
                </a>
                @endforeach
            </div>
            @else
            <div class="card p-12 text-center text-gray-400">
                <i class="fas fa-heart text-4xl block mb-3 opacity-30"></i>
                <p class="font-medium text-gray-600">Wishlist is empty</p>
                <a href="{{ url('/products') }}" class="btn-primary inline-block mt-4 px-6 py-2 rounded-xl text-sm font-semibold">Discover</a>
            </div>
            @endif
            @endif

            {{-- ════ ADDRESSES ════ --}}
            @if($activeTab === 'addresses')
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-xl font-bold text-gray-900">Address Book</h2>
                <button wire:click="$toggle('showAddressForm')"
                        class="btn-primary px-4 py-2 rounded-xl text-sm font-semibold flex items-center gap-2">
                    <i class="fas fa-plus"></i> Add Address
                </button>
            </div>
            @if($showAddressForm)
            <div class="card p-6 mb-5">
                <h3 class="font-bold text-gray-900 mb-4">New Address</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase">Name *</label>
                        <input wire:model="addr_name" type="text" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-indigo-400">
                        @error('addr_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase">Phone *</label>
                        <input wire:model="addr_phone" type="tel" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-indigo-400">
                        @error('addr_phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase">Address *</label>
                        <input wire:model="addr_address" type="text" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-indigo-400">
                        @error('addr_address')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase">City *</label>
                        <input wire:model="addr_city" type="text" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-indigo-400">
                        @error('addr_city')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase">Postal Code</label>
                        <input wire:model="addr_postal" type="text" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-indigo-400">
                    </div>
                    <div class="md:col-span-2">
                        <label class="flex items-center gap-2 text-sm font-medium text-gray-700 cursor-pointer">
                            <input type="checkbox" wire:model="addr_is_default" class="rounded"> Set as default address
                        </label>
                    </div>
                </div>
                <div class="flex gap-2 mt-5">
                    <button wire:click="saveAddress" wire:loading.attr="disabled"
                            class="btn-primary px-6 py-2.5 rounded-xl font-semibold text-sm">
                        <span wire:loading.remove wire:target="saveAddress">Save Address</span>
                        <span wire:loading wire:target="saveAddress"><i class="fas fa-spinner fa-spin mr-1"></i>Saving...</span>
                    </button>
                    <button wire:click="$set('showAddressForm', false)"
                            class="px-6 py-2.5 rounded-xl font-semibold text-sm border border-gray-200 text-gray-600 hover:bg-gray-50">Cancel</button>
                </div>
            </div>
            @endif
            @if($addresses->isNotEmpty())
            <div class="space-y-3">
                @foreach($addresses as $addr)
                <div class="card p-5 flex items-start justify-between">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-map-marker-alt text-indigo-600"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2 mb-0.5">
                                <p class="font-semibold text-gray-900 text-sm">{{ $addr->name }}</p>
                                @if($addr->is_default)<span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-semibold">Default</span>@endif
                            </div>
                            <p class="text-sm text-gray-500">{{ $addr->address }}, {{ $addr->city }}</p>
                            <p class="text-xs text-gray-400">{{ $addr->phone }}</p>
                        </div>
                    </div>
                    <div class="flex gap-1">
                        <button class="text-xs font-semibold text-indigo-600 px-2 py-1 rounded-lg hover:bg-indigo-50 transition">Edit</button>
                        <button class="text-xs font-semibold text-red-500 px-2 py-1 rounded-lg hover:bg-red-50 transition">Delete</button>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="card p-12 text-center text-gray-400">
                <i class="fas fa-map-marker-alt text-4xl block mb-3 opacity-30"></i>
                <p class="font-medium text-gray-600">No saved addresses</p>
                <p class="text-sm mt-1">Add an address for faster checkout</p>
            </div>
            @endif
            @endif

            {{-- ════ CARDS ════ --}}
            @if($activeTab === 'cards')
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-xl font-bold text-gray-900">Payment Cards</h2>
                <button wire:click="$toggle('showCardForm')"
                        class="btn-primary px-4 py-2 rounded-xl text-sm font-semibold flex items-center gap-2">
                    <i class="fas fa-plus"></i> Add Card
                </button>
            </div>
            @if($showCardForm)
            <div class="card p-6 mb-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase">Card Number</label>
                        <input wire:model="card_number" type="text" placeholder="1234 5678 9012 3456" maxlength="19"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-mono focus:outline-none focus:border-indigo-400">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase">Cardholder Name</label>
                        <input wire:model="card_name" type="text" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-indigo-400">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase">Expiry (MM/YY)</label>
                        <input wire:model="card_expiry" type="text" placeholder="MM/YY" maxlength="5"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-mono focus:outline-none focus:border-indigo-400">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase">CVV</label>
                        <input wire:model="card_cvv" type="text" placeholder="•••" maxlength="4"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-mono focus:outline-none focus:border-indigo-400">
                    </div>
                </div>
                <p class="text-xs text-gray-400 mt-3 flex items-center gap-1.5">
                    <i class="fas fa-lock text-green-500"></i> Your card details are encrypted and secure
                </p>
                <div class="flex gap-2 mt-4">
                    <button class="btn-primary px-6 py-2.5 rounded-xl font-semibold text-sm">Save Card</button>
                    <button wire:click="$set('showCardForm', false)"
                            class="px-6 py-2.5 rounded-xl font-semibold text-sm border border-gray-200 text-gray-600 hover:bg-gray-50">Cancel</button>
                </div>
            </div>
            @endif
            <div class="card p-12 text-center text-gray-400">
                <i class="fas fa-credit-card text-4xl block mb-3 opacity-30"></i>
                <p class="font-medium text-gray-600">No saved cards</p>
                <p class="text-sm mt-1">Save a card for faster checkout</p>
            </div>
            @endif

            {{-- ════ SETTINGS ════ --}}
            @if($activeTab === 'settings')
            <h2 class="text-xl font-bold text-gray-900 mb-5">Profile Settings</h2>

            {{-- Photo card --}}
            <div class="card p-6 mb-4">
                <h3 class="font-bold text-gray-900 mb-4">Profile Photo</h3>
                <div class="flex items-center gap-5">
                    <div class="w-20 h-20 rounded-full overflow-hidden flex items-center justify-center text-2xl font-bold text-white flex-shrink-0 relative group"
                         style="background:var(--c-primary)">
                        @if($profile_photo)
                            <img src="{{ $profile_photo->temporaryUrl() }}" class="w-full h-full object-cover">
                        @elseif($user->profile_photo_path ?? null)
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($user->profile_photo_path) }}"
                                 class="w-full h-full object-cover" wire:key="settings-avatar-{{ $user->updated_at }}">
                        @else
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        @endif
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <label class="btn-outline px-4 py-2 rounded-xl text-sm font-semibold cursor-pointer inline-block">
                                <i class="fas fa-upload mr-2"></i>Choose Photo
                                <input type="file" wire:model="profile_photo" accept="image/*" class="hidden">
                            </label>
                            @if($user->profile_photo_path ?? null)
                            <button wire:click="removePhoto"
                                    wire:confirm="Remove your profile photo?"
                                    class="px-4 py-2 rounded-xl text-sm font-semibold border border-red-200 text-red-500 hover:bg-red-50 transition">
                                <i class="fas fa-trash mr-1"></i>Remove
                            </button>
                            @endif
                        </div>
                        <p class="text-xs text-gray-400 mt-2">JPG, PNG, GIF or WebP. Max 2MB.</p>
                        @error('profile_photo')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        @if($profile_photo)
                        <div class="flex items-center gap-2 mt-3">
                            <button wire:click="savePhoto" wire:loading.attr="disabled"
                                    class="btn-primary px-5 py-2 rounded-xl font-semibold text-sm flex items-center gap-2">
                                <span wire:loading.remove wire:target="savePhoto"><i class="fas fa-check mr-1"></i>Save Photo</span>
                                <span wire:loading wire:target="savePhoto"><i class="fas fa-spinner fa-spin mr-1"></i>Saving...</span>
                            </button>
                            <button wire:click="$set('profile_photo', null)"
                                    class="px-4 py-2 rounded-xl text-sm border border-gray-200 text-gray-500 hover:bg-gray-50 transition">
                                Cancel
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Personal info --}}
            <div class="card p-6 mb-4">
                <h3 class="font-bold text-gray-900 mb-4">Personal Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase">Full Name *</label>
                        <input wire:model="name" type="text" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-indigo-400">
                        @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase">Email *</label>
                        <input wire:model="email" type="email" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-indigo-400">
                        @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase">Phone</label>
                        <input wire:model="phone" type="tel" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-indigo-400">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase">Date of Birth</label>
                        <input wire:model="dob" type="date" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-indigo-400">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase">Default Address</label>
                        <textarea wire:model="address" rows="2"
                                  class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm resize-none focus:outline-none focus:border-indigo-400"
                                  placeholder="Your delivery address..."></textarea>
                    </div>
                </div>
            </div>

            {{-- Preferences --}}
            <div class="card p-6 mb-5">
                <h3 class="font-bold text-gray-900 mb-4">Notification Preferences</h3>
                <div class="space-y-4">
                    @foreach([
                        ['email_offers',  'Email Promotions',    'Receive deals and offers via email'],
                        ['sms_alerts',    'SMS Alerts',          'Get order updates via SMS'],
                        ['order_updates', 'Order Notifications', 'Status change alerts'],
                    ] as [$prop, $label, $desc])
                    <div class="flex items-center justify-between py-1">
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $label }}</p>
                            <p class="text-xs text-gray-400">{{ $desc }}</p>
                        </div>
                        <button wire:click="$toggle('{{ $prop }}')"
                                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors duration-200"
                                style="{{ $this->$prop ? 'background:var(--c-primary)' : 'background:#e5e7eb' }}">
                            <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform duration-200"
                                  style="{{ $this->$prop ? 'transform:translateX(20px)' : 'transform:translateX(4px)' }}"></span>
                        </button>
                    </div>
                    @endforeach
                </div>
            </div>

            <button wire:click="saveProfile" wire:loading.attr="disabled"
                    class="btn-primary px-8 py-3 rounded-2xl font-bold text-sm flex items-center gap-2">
                <span wire:loading.remove wire:target="saveProfile"><i class="fas fa-save mr-1"></i>Save Changes</span>
                <span wire:loading wire:target="saveProfile"><i class="fas fa-spinner fa-spin mr-1"></i>Saving...</span>
            </button>
            @endif

            {{-- ════ SECURITY ════ --}}
            @if($activeTab === 'security')
            <h2 class="text-xl font-bold text-gray-900 mb-5">Security Settings</h2>

            {{-- Change Password --}}
            <div class="card p-6 mb-4">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center">
                        <i class="fas fa-lock text-indigo-600"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">Change Password</h3>
                        <p class="text-xs text-gray-400">Min 8 characters with numbers & mixed case</p>
                    </div>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase">Current Password</label>
                        <div class="relative">
                            <input wire:model="current_password" type="{{ $showCurrentPw ? 'text' : 'password' }}"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm pr-10 focus:outline-none focus:border-indigo-400">
                            <button type="button" wire:click="$toggle('showCurrentPw')" class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600">
                                <i class="fas {{ $showCurrentPw ? 'fa-eye-slash' : 'fa-eye' }} text-sm"></i>
                            </button>
                        </div>
                        @error('current_password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase">New Password</label>
                        <div class="relative">
                            <input wire:model.live="password" type="{{ $showNewPw ? 'text' : 'password' }}"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm pr-10 focus:outline-none focus:border-indigo-400">
                            <button type="button" wire:click="$toggle('showNewPw')" class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600">
                                <i class="fas {{ $showNewPw ? 'fa-eye-slash' : 'fa-eye' }} text-sm"></i>
                            </button>
                        </div>
                        @php
                            $s=0;
                            if(strlen($password??'')>=8) $s++;
                            if(preg_match('/[A-Z]/',$password??'')) $s++;
                            if(preg_match('/[0-9]/',$password??'')) $s++;
                            if(preg_match('/[^A-Za-z0-9]/',$password??'')) $s++;
                            $sc=['#ef4444','#f97316','#eab308','#22c55e'];
                            $sl=['Too weak','Weak','Good','Strong'];
                        @endphp
                        @if($password)
                        <div class="mt-2 flex gap-1">
                            @for($i=0;$i<4;$i++)
                            <div class="h-1.5 flex-1 rounded-full transition-all" style="background:{{ $i<$s?$sc[$s-1]:'#e5e7eb' }}"></div>
                            @endfor
                        </div>
                        <p class="text-xs mt-1 font-medium" style="color:{{ $s>0?$sc[$s-1]:'#9ca3af' }}">{{ $s>0?$sl[$s-1]:'' }}</p>
                        @endif
                        @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase">Confirm Password</label>
                        <input wire:model="password_confirmation" type="password"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-indigo-400">
                    </div>
                </div>
                <button wire:click="updatePassword" wire:loading.attr="disabled"
                        class="mt-5 btn-primary px-6 py-2.5 rounded-xl font-semibold text-sm">
                    <span wire:loading.remove wire:target="updatePassword">Update Password</span>
                    <span wire:loading wire:target="updatePassword"><i class="fas fa-spinner fa-spin mr-1"></i>Updating...</span>
                </button>
            </div>

            {{-- 2FA --}}
            <div class="card p-6 mb-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center">
                            <i class="fas fa-shield-alt text-green-600"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900">Two-Step Verification</h3>
                            <p class="text-xs text-gray-400">Extra layer of account security</p>
                        </div>
                    </div>
                    @php $twoFAEnabled = !!(auth()->user()->two_factor_secret ?? null); @endphp
                    <button wire:click="toggle2FA({{ $twoFAEnabled ? 'false' : 'true' }})"
                            class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors duration-200"
                            style="{{ $twoFAEnabled ? 'background:var(--c-primary)' : 'background:#e5e7eb' }}">
                        <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform duration-200"
                              style="{{ $twoFAEnabled ? 'transform:translateX(20px)' : 'transform:translateX(4px)' }}"></span>
                    </button>
                </div>
            </div>

            {{-- Active sessions --}}
            <div class="card p-6 mb-4">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-orange-100 flex items-center justify-center">
                        <i class="fas fa-desktop text-orange-600"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">Active Sessions</h3>
                        <p class="text-xs text-gray-400">Sign out other devices</p>
                    </div>
                </div>
                <div class="flex items-center justify-between py-3 border-b border-gray-50 mb-4">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-laptop text-gray-400 text-lg"></i>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">Current Device</p>
                            <p class="text-xs text-gray-400">{{ request()->ip() }}</p>
                        </div>
                    </div>
                    <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full font-semibold">Active</span>
                </div>
                <div class="flex gap-2">
                    <input wire:model="logoutPassword" type="password"
                           placeholder="Confirm your password to sign out others"
                           class="flex-1 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-indigo-400">
                    <button wire:click="logoutOtherDevices" wire:loading.attr="disabled"
                            class="px-4 py-2 rounded-xl text-sm font-semibold text-red-600 border border-red-200 hover:bg-red-50 transition">
                        Sign Out Others
                    </button>
                </div>
            </div>

            {{-- Danger zone --}}
            <div class="card p-6 border border-red-100">
                <h3 class="font-bold text-red-600 mb-2 flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle"></i> Danger Zone
                </h3>
                <p class="text-sm text-gray-500 mb-4">This action is permanent and cannot be undone.</p>
                <form action="{{ url('/profile/delete') }}" method="POST"
                      onsubmit="return confirm('Are you absolutely sure? All your data will be deleted.')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="px-5 py-2.5 rounded-xl text-sm font-semibold text-red-600 border border-red-200 hover:bg-red-50 transition">
                        <i class="fas fa-trash mr-2"></i>Delete Account
                    </button>
                </form>
            </div>
            @endif

            {{-- ════ CONNECTED ACCOUNTS ════ --}}
            @if($activeTab === 'connected')
            <h2 class="text-xl font-bold text-gray-900 mb-5">Connected Accounts</h2>
            <p class="text-sm text-gray-500 mb-5">Link your social accounts for faster sign-in.</p>
            <div class="space-y-3">
                @foreach([
                    ['google',   'Google',   '#4285F4', 'fab fa-google',    $user->google_id   ?? null],
                    ['facebook', 'Facebook', '#1877F2', 'fab fa-facebook-f', $user->facebook_id ?? null],
                    ['apple',    'Apple',    '#000000', 'fab fa-apple',     null],
                ] as [$provider, $name, $color, $icon, $connected])
                <div class="card p-5 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center flex-shrink-0"
                             style="background:{{ $color }}">
                            <i class="{{ $icon }} text-white text-xl"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">{{ $name }}</p>
                            <p class="text-xs text-gray-400">{{ $connected ? 'Connected — '.$user->email : 'Not connected' }}</p>
                        </div>
                    </div>
                    @if($connected)
                    <button class="px-4 py-2 rounded-xl text-sm font-semibold text-red-500 border border-red-200 hover:bg-red-50 transition">Disconnect</button>
                    @else
                    <a href="{{ url('/auth/'.$provider) }}"
                       class="btn-primary px-4 py-2 rounded-xl text-sm font-semibold">Connect</a>
                    @endif
                </div>
                @endforeach
            </div>
            @endif

        </main>
    </div>
</div>
</div>
@php
    use Illuminate\Support\Facades\Storage;
    $request = request();
@endphp

<div>
    @if($layout['topbarEnabled'])
        <div class="px-4 py-2 text-xs font-semibold text-white" style="background:linear-gradient(90deg, {{ $layout['topbarFrom'] }}, {{ $layout['topbarTo'] }})">
            <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-center gap-4 sm:justify-between">
                <span><i class="fas fa-bolt mr-2 text-[10px]"></i>{{ $layout['utilityBadge'] }}</span>
                <div class="hidden items-center gap-5 sm:flex">
                    <span>{{ $layout['utilityLeft'] }}</span>
                    <span>{{ $layout['utilityCenter'] }}</span>
                    <span>{{ $layout['topbarText'] }}</span>
                </div>
            </div>
        </div>
    @endif

    <header class="sticky top-0 z-50 px-4 py-4">
        <div class="glass card-shadow mx-auto flex max-w-7xl items-center justify-between rounded-[2rem] px-4 py-3 lg:px-6">
            <div class="flex items-center gap-4">
                <a href="/" class="flex items-center gap-3">
                    @if($layout['logoPath'])
                        <img src="{{ Storage::url($layout['logoPath']) }}" alt="{{ $layout['siteName'] }}" class="h-10 w-auto object-contain">
                    @else
                        <span class="text-2xl font-black lowercase" style="color:var(--primary)">{{ strtolower($layout['siteName']) }}</span>
                    @endif
                </a>
                <nav class="hidden items-center gap-5 text-sm font-medium lg:flex">
                    <a wire:navigate href="{{ url('/products') }}" class="{{ $request->is('products*') ? 'font-semibold' : '' }}">{{ $layout['navProductsLabel'] }}</a>
                    <a href="{{ url('/#categories') }}">{{ $layout['navCategoriesLabel'] }}</a>
                    @if($layout['showDealsLink'])
                        <a href="{{ url('/#deals') }}">{{ $layout['navDealsLabel'] }}</a>
                    @endif
                    <a href="{{ url('/#reviews') }}">{{ $layout['navReviewsLabel'] }}</a>
                    <a wire:navigate href="{{ route('track-order') }}">{{ $layout['navTrackLabel'] }}</a>
                    <a wire:navigate href="{{ route('help-center') }}">{{ $layout['navHelpLabel'] }}</a>
                    <a href="{{ url('/#footer') }}">Contact</a>
                </nav>
            </div>

            <form action="{{ url('/products') }}" method="GET" class="relative hidden w-full max-w-md md:block">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ $layout['searchPlaceholder'] }}" class="w-full rounded-full border border-white/40 bg-white/70 px-11 py-3 text-sm text-slate-700 outline-none dark:border-white/10 dark:bg-slate-900/60 dark:text-white">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
            </form>

            <div class="flex items-center gap-2">
                <button @click="toggle()" type="button" class="flex h-11 w-11 items-center justify-center rounded-full border border-white/30 bg-white/70 text-slate-700 dark:border-white/10 dark:bg-slate-900/60 dark:text-white">
                    <i class="fas" :class="dark ? 'fa-sun' : 'fa-moon'"></i>
                </button>

                <div class="relative" x-data="{ open:false }">
                    <button @click="open=!open" type="button" class="relative flex h-11 w-11 items-center justify-center rounded-full border border-white/30 bg-white/70 dark:border-white/10 dark:bg-slate-900/60">
                        <i class="far fa-bell"></i>
                        @if($unreadNotifications > 0)
                            <span class="absolute -right-0.5 -top-0.5 flex h-4 min-w-[16px] items-center justify-center rounded-full bg-rose-500 px-1 text-[10px] font-bold text-white">{{ min($unreadNotifications, 9) }}</span>
                        @endif
                    </button>
                    <div x-show="open" @click.away="open=false" x-transition class="absolute right-0 top-14 z-50 w-[360px] overflow-hidden rounded-[1.5rem] border border-white/20 bg-white/95 p-4 shadow-[0_24px_80px_rgba(15,23,42,0.18)] backdrop-blur dark:border-white/10 dark:bg-slate-950/95" style="display:none">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-soft">Notification Center</p>
                                <p class="mt-1 text-sm font-semibold text-adapt">Deals, price drops, stock returns, and order updates</p>
                            </div>
                            <button
                                type="button"
                                wire:click="markNotificationsSeen"
                                class="storefront-chip rounded-full px-3 py-2 text-xs font-semibold text-adapt"
                            >
                                Mark all read
                            </button>
                        </div>

                        <div class="mt-4 space-y-3">
                            @forelse($notifications as $notification)
                                @php
                                    $accentClasses = match($notification['accent'] ?? 'indigo') {
                                        'emerald' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-300',
                                        'sky' => 'bg-sky-500/10 text-sky-600 dark:text-sky-300',
                                        'amber' => 'bg-amber-500/10 text-amber-700 dark:text-amber-300',
                                        'violet' => 'bg-violet-500/10 text-violet-600 dark:text-violet-300',
                                        default => 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-300',
                                    };
                                @endphp
                                <div class="rounded-2xl border px-4 py-4 text-sm transition {{ $notification['read'] ? 'border-slate-200 bg-white/65 dark:border-white/10 dark:bg-slate-900/45' : 'border-indigo-200 bg-indigo-50/80 shadow-[0_10px_30px_rgba(99,102,241,0.12)] dark:border-indigo-400/30 dark:bg-slate-900/70' }}">
                                    <div class="flex items-start justify-between gap-3">
                                        <a href="{{ $notification['action_url'] }}" class="min-w-0 flex-1">
                                            <div class="flex items-center gap-2">
                                                <span class="rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-[0.2em] {{ $accentClasses }}">{{ $notification['label'] }}</span>
                                                @if(!$notification['read'])
                                                    <span class="h-2.5 w-2.5 rounded-full bg-indigo-500"></span>
                                                @endif
                                            </div>
                                            <p class="mt-3 font-semibold text-adapt">{{ $notification['title'] }}</p>
                                            <p class="mt-1 text-xs leading-6 text-soft">{{ $notification['body'] }}</p>
                                        </a>
                                        @if(!$notification['read'])
                                            <button
                                                type="button"
                                                wire:click="markNotificationRead('{{ $notification['id'] }}')"
                                                class="rounded-full border border-slate-200 px-3 py-2 text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-500 transition hover:border-slate-300 hover:text-slate-700 dark:border-white/10 dark:text-slate-300"
                                            >
                                                Read
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="rounded-2xl border border-dashed border-slate-300 px-4 py-8 text-center text-sm text-soft">No fresh updates yet.</div>
                            @endforelse
                        </div>

                        @if($recommended->isNotEmpty())
                            <div class="mt-5 border-t border-slate-200 pt-4 dark:border-white/10">
                                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-soft">Suggested For You</p>
                                <div class="mt-3 space-y-3">
                                    @foreach($recommended->take(2) as $product)
                                        <a href="{{ url('/products/'.$product->id) }}" class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white/80 px-3 py-3 dark:border-white/10 dark:bg-slate-900/60">
                                            <div class="h-14 w-14 overflow-hidden rounded-xl bg-slate-100 dark:bg-slate-800">
                                                @if($product->primary_image_url)
                                                    <img src="{{ $product->primary_image_sources['fallback'] ?? $product->primary_image_url }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                                                @endif
                                            </div>
                                            <div class="min-w-0">
                                                <p class="truncate text-sm font-semibold text-adapt">{{ $product->name }}</p>
                                                <p class="mt-1 text-xs text-soft">Rs {{ number_format($product->final_price ?? $product->selling_price, 2) }}</p>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <a wire:navigate href="{{ url('/wishlist') }}" class="relative flex h-11 w-11 items-center justify-center rounded-full border border-white/30 bg-white/70 dark:border-white/10 dark:bg-slate-900/60">
                    <i class="far fa-heart"></i>
                    @if($wishCount > 0)
                        <span class="absolute -right-0.5 -top-0.5 flex h-4 min-w-[16px] items-center justify-center rounded-full bg-rose-500 px-1 text-[10px] font-bold text-white">{{ $wishCount }}</span>
                    @endif
                </a>

                <a wire:navigate href="{{ url('/cart') }}" class="relative flex h-11 w-11 items-center justify-center rounded-full border border-white/30 bg-white/70 dark:border-white/10 dark:bg-slate-900/60">
                    <i class="fas fa-bag-shopping"></i>
                    @if($cartCount > 0)
                        <span class="absolute -right-0.5 -top-0.5 flex h-4 min-w-[16px] items-center justify-center rounded-full px-1 text-[10px] font-bold text-white" style="background:var(--primary)">{{ $cartCount }}</span>
                    @endif
                </a>

                @guest
                    <a wire:navigate href="{{ route('login') }}" class="hidden rounded-full px-4 py-2 text-sm font-semibold md:inline-flex">Login</a>
                    <a wire:navigate href="{{ route('register') }}" class="rounded-full px-4 py-2 text-sm font-semibold text-white" style="background:linear-gradient(90deg, var(--primary), var(--secondary))">Sign Up</a>
                @else
                    <div class="relative" x-data="{ open:false }">
                        <button @click="open=!open" type="button" class="inline-flex items-center gap-3 rounded-full border border-white/30 bg-white/70 px-3 py-2 text-sm font-semibold text-adapt dark:border-white/10 dark:bg-slate-900/60">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full text-xs font-black text-white" style="background:linear-gradient(135deg,var(--primary),var(--secondary))">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                            <span class="hidden max-w-[110px] truncate md:inline">{{ auth()->user()->name }}</span>
                            <i class="fas fa-chevron-down text-[10px] text-soft"></i>
                        </button>

                        <div x-show="open" @click.away="open=false" x-transition class="absolute right-0 top-14 z-[80] max-h-[75vh] w-80 overflow-y-auto rounded-[1.5rem] border border-white/20 bg-white/95 p-3 shadow-[0_24px_80px_rgba(15,23,42,0.18)] backdrop-blur dark:border-white/10 dark:bg-slate-950/95" style="display:none">
                            <div class="rounded-2xl bg-slate-50 px-4 py-4 dark:bg-slate-900/70">
                                <p class="truncate text-sm font-bold text-adapt">{{ auth()->user()->name }}</p>
                                <p class="mt-1 truncate text-xs text-soft">{{ auth()->user()->email }}</p>
                            </div>

                            <div class="mt-3 space-y-1.5">
                                <a wire:navigate href="{{ route('profile.index') }}" class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold text-adapt transition hover:bg-slate-50 dark:hover:bg-slate-900/70">
                                    <i class="fas fa-user w-4 text-center text-soft"></i> Profile
                                </a>
                                <a wire:navigate href="{{ route('profile.index', ['tab' => 'orders']) }}" class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold text-adapt transition hover:bg-slate-50 dark:hover:bg-slate-900/70">
                                    <i class="fas fa-bag-shopping w-4 text-center text-soft"></i> My Orders
                                </a>
                                <a wire:navigate href="{{ route('wishlist.index') }}" class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold text-adapt transition hover:bg-slate-50 dark:hover:bg-slate-900/70">
                                    <i class="fas fa-heart w-4 text-center text-soft"></i> Wishlist
                                </a>
                                @can('view-admin-menu')
                                    <a wire:navigate href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold text-white" style="background:linear-gradient(90deg,var(--primary),var(--secondary))">
                                        <i class="fas fa-gauge-high w-4 text-center"></i> Admin Panel
                                    </a>
                                @endcan
                            </div>

                            <form method="POST" action="{{ route('logout') }}" class="mt-3 border-t border-slate-200 pt-3 dark:border-white/10">
                                @csrf
                                <button type="submit" class="flex w-full items-center gap-3 rounded-2xl border border-rose-100 bg-rose-50 px-4 py-3 text-left text-sm font-semibold text-rose-600 transition hover:border-rose-200 hover:bg-rose-100 dark:border-rose-500/20 dark:bg-rose-500/10 dark:text-rose-200 dark:hover:bg-rose-500/15">
                                    <i class="fas fa-arrow-right-from-bracket w-4 text-center"></i> Sign Out
                                </button>
                            </form>
                        </div>
                    </div>
                @endguest
            </div>
        </div>
    </header>
</div>

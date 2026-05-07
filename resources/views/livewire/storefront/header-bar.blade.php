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

    <header class="sticky top-0 z-50 px-4 py-6">
        <div class="glass mx-auto flex max-w-7xl flex-col gap-4 rounded-[2.5rem] px-6 py-4 shadow-[0_25px_80px_rgba(0,0,0,0.06)]">
            <div class="flex items-center justify-between gap-8">
                <!-- Branding Protocol -->
                <a href="/" class="flex shrink-0 items-center gap-3">
                    @if($layout['logoPath'])
                        <div class="flex h-12 items-center rounded-2xl bg-white/80 px-4 dark:bg-slate-900/80">
                            <img src="{{ Storage::url($layout['logoPath']) }}" alt="{{ $layout['siteName'] }}" class="h-8 w-auto">
                        </div>
                        <span class="hidden text-xs font-black uppercase tracking-[0.3em] text-slate-900 dark:text-white lg:inline">{{ $layout['siteName'] }}</span>
                    @else
                        <span class="text-2xl font-black lowercase tracking-tighter text-slate-900 dark:text-white" style="color:var(--primary)">{{ strtolower($layout['siteName']) }}</span>
                    @endif
                </a>

                <!-- Search Protocol -->
                <form action="{{ url('/products') }}" method="GET" class="relative hidden flex-1 max-w-lg md:block group">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ $layout['searchPlaceholder'] }}" 
                           class="w-full rounded-full border border-slate-200/50 bg-slate-100/50 px-12 py-3 text-xs font-bold uppercase tracking-widest text-slate-700 outline-none transition-all duration-500 focus:border-[var(--primary)] focus:bg-white focus:ring-8 focus:ring-[var(--primary)]/5 dark:border-white/5 dark:bg-slate-900/50 dark:text-white">
                    <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-[11px] text-slate-400 group-focus-within:text-[var(--primary)] transition-colors"></i>
                </form>

                <!-- Action Hub -->
                <div class="flex shrink-0 items-center gap-3">
                    <button @click="toggle()" type="button" class="flex h-11 w-11 items-center justify-center rounded-full text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                        <i class="fas" :class="dark ? 'fa-sun' : 'fa-moon'"></i>
                    </button>

                    <div class="relative" x-data="{ open:false }">
                        <button @click="open=!open" type="button" class="relative flex h-11 w-11 items-center justify-center rounded-full text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                            <i class="far fa-bell"></i>
                            @if($unreadNotifications > 0)
                                <span class="absolute right-0 top-0 flex h-4 min-w-[16px] items-center justify-center rounded-full bg-rose-500 text-[9px] font-black text-white ring-4 ring-white dark:ring-slate-900">{{ $unreadNotifications }}</span>
                            @endif
                        </button>
                        <div x-show="open" @click.away="open=false" x-transition class="absolute right-0 top-16 z-[60] w-[380px] overflow-hidden rounded-[2rem] border border-white/20 bg-white/95 p-6 shadow-2xl backdrop-blur-xl dark:border-white/10 dark:bg-slate-950/95" style="display:none">
                            <div class="flex items-center justify-between mb-6">
                                <p class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400">Intelligence</p>
                                <button type="button" wire:click="markNotificationsSeen" class="text-[9px] font-black uppercase tracking-widest text-emerald-500 hover:underline">Flush All</button>
                            </div>
                            <div class="space-y-4 max-h-[400px] overflow-y-auto custom-scrollbar pr-2">
                                @forelse($notifications as $notification)
                                    <div class="rounded-2xl p-4 border {{ $notification['read'] ? 'border-slate-100 bg-slate-50/50' : 'border-indigo-100 bg-indigo-50/50 shadow-sm' }} dark:border-white/5 dark:bg-slate-900/50">
                                        <p class="text-xs font-black text-slate-900 dark:text-white mb-1">{{ $notification['title'] }}</p>
                                        <p class="text-[11px] text-slate-500 leading-relaxed">{{ $notification['body'] }}</p>
                                    </div>
                                @empty
                                    <p class="text-center py-8 text-xs text-slate-400 font-bold uppercase tracking-widest">No active alerts</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <a wire:navigate href="{{ url('/cart') }}" class="relative flex h-11 w-11 items-center justify-center rounded-full text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                        <i class="fas fa-shopping-bag"></i>
                        @if($cartCount > 0)
                            <span class="absolute right-0 top-0 flex h-4 min-w-[16px] items-center justify-center rounded-full bg-slate-900 text-[9px] font-black text-white ring-4 ring-white dark:ring-slate-900" style="background:var(--primary)">{{ $cartCount }}</span>
                        @endif
                    </a>

                    @guest
                        <div class="ml-2 hidden items-center gap-3 lg:flex">
                            <a wire:navigate href="{{ route('login') }}" class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-700 dark:text-slate-200">Login</a>
                            <a wire:navigate href="{{ route('register') }}" class="rounded-full px-6 py-3 text-[10px] font-black uppercase tracking-[0.2em] text-white shadow-xl hover:scale-105 transition-all" style="background:linear-gradient(90deg, var(--primary), var(--secondary))">Sign Up</a>
                        </div>
                    @else
                        <div class="relative ml-2" x-data="{ open:false }">
                            <button @click="open=!open" type="button" class="flex items-center gap-3 rounded-full bg-slate-100/50 p-1 pr-4 dark:bg-slate-800/50 transition-all hover:bg-white dark:hover:bg-slate-800">
                                <div class="h-9 w-9 rounded-full flex items-center justify-center text-[10px] font-black text-white" style="background:linear-gradient(135deg,var(--primary),var(--secondary))">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                                <span class="text-[10px] font-black uppercase tracking-widest text-slate-700 dark:text-slate-200">{{ explode(' ', auth()->user()->name)[0] }}</span>
                                <i class="fas fa-chevron-down text-[8px] text-slate-400"></i>
                            </button>
                            <div x-show="open" @click.away="open=false" x-transition class="absolute right-0 top-14 z-[60] w-64 overflow-hidden rounded-[1.5rem] border border-white/20 bg-white p-2 shadow-2xl dark:border-white/5 dark:bg-slate-950" style="display:none">
                                <a wire:navigate href="{{ route('profile.index') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-[10px] font-black uppercase tracking-widest text-slate-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-900">
                                    <i class="fas fa-user-circle text-xs"></i> Profile
                                </a>
                                <a wire:navigate href="{{ route('profile.index', ['tab' => 'orders']) }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-[10px] font-black uppercase tracking-widest text-slate-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:bg-slate-900">
                                    <i class="fas fa-box text-xs"></i> Orders
                                </a>
                                @can('view-admin-menu')
                                    <a wire:navigate href="{{ route('admin.dashboard') }}" class="mt-1 flex items-center gap-3 rounded-xl px-4 py-3 text-[10px] font-black uppercase tracking-widest text-white shadow-lg" style="background:linear-gradient(90deg,var(--primary),var(--secondary))">
                                        <i class="fas fa-gauge-high text-xs"></i> Admin Panel
                                    </a>
                                @endcan
                                <form method="POST" action="{{ route('logout') }}" class="mt-2 pt-2 border-t border-slate-50 dark:border-white/5">
                                    @csrf
                                    <button type="submit" class="flex w-full items-center gap-3 rounded-xl px-4 py-3 text-[10px] font-black uppercase tracking-widest text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-500/10">
                                        <i class="fas fa-power-off text-xs"></i> Sign Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endguest
                </div>
            </div>

            <!-- Navigation Protocol -->
            <div class="hidden border-t border-slate-100 pt-4 lg:flex dark:border-white/5">
                <nav class="flex w-full items-center justify-center gap-10">
                    @foreach([
                        ['url' => '/products', 'label' => $layout['navProductsLabel']],
                        ['url' => '/#categories', 'label' => $layout['navCategoriesLabel']],
                        ['url' => '/#deals', 'label' => $layout['navDealsLabel'], 'condition' => $layout['showDealsLink']],
                        ['url' => route('track-order'), 'label' => $layout['navTrackLabel']],
                        ['url' => route('help-center'), 'label' => $layout['navHelpLabel']]
                    ] as $nav)
                        @if(!isset($nav['condition']) || $nav['condition'])
                            <a href="{{ $nav['url'] }}" class="relative group py-1">
                                <span class="text-[10px] font-black uppercase tracking-[0.25em] text-slate-500 transition-colors group-hover:text-slate-900 dark:group-hover:text-white">{{ $nav['label'] }}</span>
                                <span class="absolute -bottom-1 left-0 h-[2px] w-0 bg-slate-900 transition-all duration-300 group-hover:w-full dark:bg-white"></span>
                            </a>
                        @endif
                    @endforeach
                </nav>
            </div>
        </div>
    </header>
</div>

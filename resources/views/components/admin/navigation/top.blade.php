@php
    $siteName = \App\Models\SiteSetting::get('site_name', config('app.name', 'Display Lanka'));
@endphp

<nav class="admin-topbar group/nav">
    <div class="admin-topbar__inner px-6 lg:px-8">
        <div class="flex items-center gap-6">
            <!-- Mobile Toggle -->
            <button @click="toggleSidebar" class="lg:hidden flex h-10 w-10 items-center justify-center rounded-xl bg-white/10 text-white hover:bg-white/20 transition-all">
                <i :class="sidebarOpen ? 'fa-xmark' : 'fa-bars-staggered'" class="fas text-sm"></i>
            </button>

            <!-- Desktop Collapse -->
            <button @click="toggleSidebar" class="hidden lg:flex h-10 w-10 items-center justify-center rounded-xl bg-white/10 text-white hover:bg-white/20 transition-all group-hover/nav:scale-105">
                <i :class="sidebarOpen ? 'fa-indent' : 'fa-outdent'" class="fas text-sm"></i>
            </button>

            <!-- Brand Logo -->
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg shadow-indigo-500/20">
                    <i class="fas fa-bolt text-white text-xs"></i>
                </div>
                <div class="hidden sm:block">
                    <h1 class="text-sm font-black tracking-tighter text-white uppercase">{{ $siteName }}</h1>
                    <p class="text-[9px] font-bold text-white/40 uppercase tracking-[0.2em] leading-none mt-0.5">Control Panel</p>
                </div>
            </div>
        </div>

        <!-- Global Command Center -->
        <div class="hidden lg:flex flex-1 justify-center max-w-xl px-12">
            <x-admin.navigation.search />
        </div>

        <!-- Action Matrix -->
        <div class="flex items-center gap-3">
            <!-- Pos Trigger -->
            @if (Route::has('admin.pos'))
                @can('view pos')
                    <button @click="launchPos('{{ route('admin.pos') }}')" class="hidden md:flex items-center gap-2.5 px-5 py-2.5 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 hover:bg-emerald-500 hover:text-white transition-all shadow-lg shadow-emerald-500/5">
                        <i class="fas fa-cash-register text-xs"></i>
                        <span class="text-[10px] font-black uppercase tracking-widest">POS Console</span>
                    </button>
                @endcan
            @endif

            <!-- System Alerts -->
            <div class="flex items-center gap-2 border-l border-white/10 pl-3 ml-1">
                @can('view system health')
                    <a href="{{ route('admin.system-health') }}" wire:navigate class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/5 text-white/60 hover:text-white hover:bg-white/10 transition-all relative">
                        <i class="fas fa-heart-pulse text-xs"></i>
                        <div class="absolute top-2.5 right-2.5 h-2 w-2 rounded-full bg-emerald-500 border-2 border-[#020617]"></div>
                    </a>
                @endcan

                <button @click="toggleTheme" class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/5 text-white/60 hover:text-white hover:bg-white/10 transition-all">
                    <i :class="theme === 'light' ? 'fa-moon' : 'fa-sun'" class="fas text-xs"></i>
                </button>
            </div>

            <!-- Profile Entry -->
            <div class="border-l border-white/10 pl-3 ml-1">
                <x-admin.navigation.user-menu />
            </div>
        </div>
    </div>
</nav>

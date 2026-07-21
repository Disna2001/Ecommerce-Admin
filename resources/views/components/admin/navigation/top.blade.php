@php
    $siteName = \App\Models\SiteSetting::get('site_name', config('app.name', 'Display Lanka'));
@endphp

<nav class="admin-topbar group/nav">
    <div class="admin-topbar__inner px-3 sm:px-4">
        <div class="flex items-center gap-3">
            <!-- Mobile Toggle -->
            <button @click="toggleSidebar" class="lg:hidden flex h-7 w-7 items-center justify-center rounded-lg bg-white/10 text-white hover:bg-white/20 transition-all">
                <i :class="sidebarOpen ? 'fa-xmark' : 'fa-bars-staggered'" class="fas text-xs"></i>
            </button>

            <!-- Desktop Collapse -->
            <button @click="toggleSidebar" class="hidden lg:flex h-7 w-7 items-center justify-center rounded-lg bg-white/10 text-white hover:bg-white/20 transition-all">
                <i :class="sidebarOpen ? 'fa-indent' : 'fa-outdent'" class="fas text-xs"></i>
            </button>

            <!-- Single-Line Brand Lockup -->
            <div class="flex items-center gap-2">
                <div class="h-7 w-7 rounded-lg bg-indigo-600 flex items-center justify-center shadow-xs flex-shrink-0">
                    <i class="fas fa-bolt text-white text-xs"></i>
                </div>
                <h1 class="text-xs font-extrabold tracking-wide text-white uppercase whitespace-nowrap">{{ $siteName }}</h1>
            </div>
        </div>

        <!-- Compact Global Search Bar -->
        <div class="hidden lg:flex flex-1 justify-center max-w-md px-4">
            <x-admin.navigation.search />
        </div>

        <!-- Action Matrix -->
        <div class="flex items-center gap-2">
            <!-- POS Console Button -->
            @if (Route::has('admin.pos'))
                @can('view pos')
                    <button @click="launchPos('{{ route('admin.pos') }}')" class="hidden md:flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 hover:bg-emerald-500 hover:text-white transition-all text-xs font-semibold shadow-xs">
                        <i class="fas fa-cash-register text-xs"></i>
                        <span class="text-[10px] font-bold uppercase tracking-wider">POS</span>
                    </button>
                @endcan
            @endif

            <!-- Quick Action Tools -->
            <div class="flex items-center gap-1.5 border-l border-white/10 pl-2">
                @can('view system health')
                    <a href="{{ route('admin.system-health') }}" wire:navigate class="flex h-7 w-7 items-center justify-center rounded-lg bg-white/10 text-white/80 hover:text-white hover:bg-white/20 transition-all relative">
                        <i class="fas fa-heart-pulse text-xs"></i>
                        <span class="absolute top-1.5 right-1.5 h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                    </a>
                @endcan

                <button @click="toggleTheme" class="flex h-7 w-7 items-center justify-center rounded-lg bg-white/10 text-white/80 hover:text-white hover:bg-white/20 transition-all">
                    <i :class="theme === 'light' ? 'fa-moon' : 'fa-sun'" class="fas text-xs"></i>
                </button>
            </div>

            <!-- User Menu -->
            <div class="border-l border-white/10 pl-2">
                <x-admin.navigation.user-menu />
            </div>
        </div>
    </div>
</nav>

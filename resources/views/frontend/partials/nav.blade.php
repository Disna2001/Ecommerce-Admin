{{--
    Shared nav partial
    Props: $siteName, $logoPath, $primaryColor, $secondaryColor, $textColor, $navBgColor
--}}
<header class="shadow-sm sticky top-0 z-50" style="background:{{ $navBgColor ?? '#fff' }}">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between h-16 md:h-20">
            <div class="flex items-center space-x-6">
                <a href="/" class="flex items-center gap-3">
                    @if(!empty($logoPath))
                        <span class="flex h-11 items-center rounded-2xl border border-gray-200 bg-white px-3 shadow-sm">
                            <img src="{{ Storage::url($logoPath) }}" alt="{{ $siteName ?? '' }}" class="block h-8 w-auto max-w-[7rem] object-contain">
                        </span>
                        <span class="hidden max-w-[9rem] truncate text-xs font-bold uppercase tracking-[0.22em] text-gray-700 md:inline">{{ $siteName ?? 'Shop' }}</span>
                    @else
                        <span class="text-xl font-extrabold" style="color:{{ $textColor ?? '#111' }}">{{ $siteName ?? 'Shop' }}</span>
                    @endif
                </a>
                <nav class="hidden lg:flex items-center space-x-1 text-sm">
                    <a href="{{ url('/products') }}" class="px-3 py-2 text-gray-600 hover:text-gray-900 font-medium rounded-lg hover:bg-gray-50">Products</a>
                    <a href="{{ url('/products?sort=newest') }}" class="px-3 py-2 text-gray-600 hover:text-gray-900 font-medium rounded-lg hover:bg-gray-50">New Arrivals</a>
                    <a href="{{ url('/products?sort=price_asc') }}" class="px-3 py-2 text-gray-600 hover:text-gray-900 font-medium rounded-lg hover:bg-gray-50">Deals</a>
                </nav>
            </div>

            {{-- Search --}}
            <form action="{{ url('/products') }}" method="GET" class="hidden md:flex flex-1 max-w-lg mx-6">
                <div class="relative w-full">
                    <input type="text" name="search" placeholder="Search products..."
                           value="{{ request('search') }}"
                           class="w-full pl-10 pr-20 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-400 bg-gray-50">
                    <i class="fas fa-search absolute left-3 top-2.5 text-gray-300 text-xs"></i>
                    <button type="submit" class="absolute right-1.5 top-1 px-3 py-1.5 rounded-lg text-white text-xs font-semibold" style="background:{{ $primaryColor ?? '#4f46e5' }}">Go</button>
                </div>
            </form>

            {{-- Right Icons --}}
            <div class="flex items-center gap-1">
                {{-- Wishlist --}}
                <a href="{{ url('/wishlist') }}" class="relative p-2.5 text-gray-500 hover:text-red-500 transition rounded-xl hover:bg-red-50">
                    <i class="fas fa-heart text-lg"></i>
                    <span class="wishlist-count absolute -top-0.5 -right-0.5 text-white text-xs rounded-full h-4 w-4 items-center justify-center hidden" style="background:{{ $primaryColor ?? '#4f46e5' }}"></span>
                </a>
                {{-- Cart --}}
                <a href="{{ url('/cart') }}" class="relative p-2.5 text-gray-500 hover:text-gray-900 transition rounded-xl hover:bg-gray-100">
                    <i class="fas fa-shopping-bag text-lg"></i>
                    <span class="cart-count absolute -top-0.5 -right-0.5 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center" style="background:{{ $primaryColor ?? '#4f46e5' }}">0</span>
                </a>
                {{-- Auth --}}
                @if(Route::has('login'))
                    @auth
                        <div class="flex items-center gap-2 ml-1">
                            <x-admin.navigation.user-menu />
                            @can('view-admin-menu')
                            <a href="{{ route('admin.dashboard') }}" class="hidden md:block text-white px-3 py-2 rounded-xl text-xs font-semibold"
                               style="background:linear-gradient(to right,{{ $secondaryColor ?? '#7c3aed' }},{{ $primaryColor ?? '#4f46e5' }})">
                                <i class="fas fa-cog mr-1"></i>Admin
                            </a>
                            @endcan
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="ml-1 px-4 py-2 border border-gray-200 rounded-xl text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
                            Login
                        </a>
                    @endauth
                @endif
            </div>
        </div>
    </div>
</header>

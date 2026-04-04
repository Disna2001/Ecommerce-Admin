{{--
    Shared footer partial
    Props: $siteName, $logoPath, $primaryColor, $secondaryColor, $textColor, $bgColor
--}}
@php
    $fbUrl = \App\Models\SiteSetting::get('facebook_url', '#');
    $twUrl = \App\Models\SiteSetting::get('twitter_url', '#');
    $igUrl = \App\Models\SiteSetting::get('instagram_url', '#');
    $piUrl = \App\Models\SiteSetting::get('pinterest_url', '#');
    $footerTagline = \App\Models\SiteSetting::get('footer_tagline', 'Your one-stop shop.');
    $footerCopyright = \App\Models\SiteSetting::get('footer_copyright', '© '.date('Y').' '.($siteName ?? 'Shop').'. All rights reserved.');
    $supportEmail = \App\Models\SiteSetting::get('support_email', \App\Models\SiteSetting::get('support_notification_email', ''));
    $supportPhone = \App\Models\SiteSetting::get('support_phone', '');
    $cats = \App\Models\Category::take(5)->get();
@endphp
<footer class="bg-gray-900 text-white pt-12 pb-6 mt-16">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 gap-8 mb-8 md:grid-cols-4">
            <div>
                @if(!empty($logoPath))
                    <img src="{{ Storage::url($logoPath) }}" alt="{{ $siteName ?? '' }}" class="h-9 mb-3 object-contain brightness-0 invert">
                @else
                    <span class="text-xl font-bold">{{ $siteName ?? 'Shop' }}</span>
                @endif
                <p class="text-gray-400 text-sm mt-2">{{ $footerTagline }}</p>
                <div class="flex gap-3 mt-4">
                    @foreach([['fab fa-facebook-f', $fbUrl], ['fab fa-twitter', $twUrl], ['fab fa-instagram', $igUrl], ['fab fa-pinterest', $piUrl]] as [$icon, $url])
                        <a href="{{ $url }}" class="w-9 h-9 bg-gray-800 rounded-full flex items-center justify-center hover:bg-indigo-600 transition text-sm">
                            <i class="{{ $icon }}"></i>
                        </a>
                    @endforeach
                </div>
            </div>
            <div>
                <h4 class="font-semibold mb-4 text-sm uppercase tracking-wider text-gray-300">Shop</h4>
                <ul class="space-y-2 text-gray-400 text-sm">
                    <li><a href="{{ url('/products') }}" class="hover:text-white transition">All Products</a></li>
                    <li><a href="{{ url('/products?sort=newest') }}" class="hover:text-white transition">New Arrivals</a></li>
                    <li><a href="{{ url('/products?sort=price_asc') }}" class="hover:text-white transition">Best Deals</a></li>
                    <li><a href="{{ route('track-order') }}" class="hover:text-white transition">Track Order</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold mb-4 text-sm uppercase tracking-wider text-gray-300">Categories</h4>
                <ul class="space-y-2 text-gray-400 text-sm">
                    @foreach($cats as $cat)
                        <li><a href="{{ url('/products?category='.$cat->id) }}" class="hover:text-white transition">{{ $cat->name }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div>
                <h4 class="font-semibold mb-4 text-sm uppercase tracking-wider text-gray-300">Help</h4>
                <ul class="space-y-2 text-gray-400 text-sm">
                    <li><a href="{{ route('help-center') }}" class="hover:text-white transition">Help Center</a></li>
                    <li><a href="{{ route('track-order') }}" class="hover:text-white transition">Track Order</a></li>
                    @if($supportEmail)
                        <li><a href="mailto:{{ $supportEmail }}" class="hover:text-white transition">{{ $supportEmail }}</a></li>
                    @endif
                    @if($supportPhone)
                        <li><a href="tel:{{ preg_replace('/\s+/', '', $supportPhone) }}" class="hover:text-white transition">{{ $supportPhone }}</a></li>
                    @endif
                </ul>
            </div>
        </div>
        <div class="border-t border-gray-800 pt-6 flex flex-col items-center justify-between gap-3 text-sm text-gray-500 md:flex-row">
            <p>{{ $footerCopyright }}</p>
            <div class="flex items-center gap-4">
                <i class="fab fa-cc-visa text-2xl text-gray-600"></i>
                <i class="fab fa-cc-mastercard text-2xl text-gray-600"></i>
                <i class="fas fa-shield-alt text-gray-600"></i>
            </div>
        </div>
    </div>
</footer>

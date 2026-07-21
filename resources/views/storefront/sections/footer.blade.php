@props(['config' => []])
@php
    $brandName = $config['brand_name'] ?? 'DISPLAY LANKA.LK';
    $aboutText = $config['about_text'] ?? 'Sri Lanka\'s leading importer and distributor of authentic smartphone display assemblies.';
    $contactEmail = $config['contact_email'] ?? 'support@displaylanka.lk';
    $contactPhone = $config['contact_phone'] ?? '+94 77 123 4567';
    $copyrightText = $config['copyright_text'] ?? '© 2026 Display Lanka LK. All rights reserved.';
@endphp

<footer class="mt-16 bg-slate-900 text-slate-400 rounded-3xl p-8 lg:p-12 border border-slate-800">
    <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
        <div>
            <h3 class="text-white font-black text-xl mb-3 tracking-wider">{{ $brandName }}</h3>
            <p class="text-sm leading-relaxed text-slate-400">{{ $aboutText }}</p>
        </div>
        <div>
            <h4 class="text-white font-bold text-sm uppercase tracking-widest mb-3">Customer Service</h4>
            <ul class="space-y-2 text-sm">
                <li><a href="{{ route('help-center') }}" class="hover:text-white transition-colors">Help Center</a></li>
                <li><a href="{{ route('track-order') }}" class="hover:text-white transition-colors">Track Your Order</a></li>
                <li><a href="{{ route('refund-policy') }}" class="hover:text-white transition-colors">Refund & Return Policy</a></li>
            </ul>
        </div>
        <div>
            <h4 class="text-white font-bold text-sm uppercase tracking-widest mb-3">Contact Details</h4>
            <p class="text-sm"><i class="fas fa-envelope mr-2 text-indigo-400"></i>{{ $contactEmail }}</p>
            <p class="text-sm mt-2"><i class="fas fa-phone mr-2 text-indigo-400"></i>{{ $contactPhone }}</p>
        </div>
    </div>
    <div class="pt-6 border-t border-slate-800 text-center text-xs text-slate-500 font-medium">
        {{ $copyrightText }}
    </div>
</footer>

@props(['config' => []])
@php
    $eyebrow = $config['eyebrow'] ?? 'Special Offer';
    $title = $config['title'] ?? 'Don\'t miss out — shop the collection now!';
    $subtitle = $config['subtitle'] ?? 'Fast Sri Lanka-wide delivery & genuine quality guarantee.';
    $buttonText = $config['button_text'] ?? 'Browse Store';
    $buttonLink = $config['button_link'] ?? '/products';
    $bgFrom = $config['bg_from'] ?? '#6d28d9';
    $bgTo = $config['bg_to'] ?? '#7c3aed';
@endphp

<section class="mx-auto max-w-7xl px-4 my-4">
    <div class="relative rounded-[2.5rem] px-6 py-8 sm:px-10 sm:py-10 text-center text-white overflow-hidden shadow-xl" style="background:linear-gradient(135deg, {{ $bgFrom }}, {{ $bgTo }}, var(--accent))">
        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 20px 20px;"></div>
        <div class="relative z-10 space-y-3">
            <p class="text-[9px] font-black uppercase tracking-[0.4em] text-white/70">{{ $eyebrow }}</p>
            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black tracking-tight">{{ $title }}</h2>
            <p class="mx-auto max-w-xl text-xs sm:text-sm text-white/80">{{ $subtitle }}</p>
            <div class="pt-2">
                <a wire:navigate href="{{ url($buttonLink) }}" class="inline-flex items-center gap-3 rounded-full bg-white px-8 py-3.5 text-xs font-black text-slate-900 uppercase tracking-widest shadow-lg hover:scale-105 transition-all group">
                    <span>{{ $buttonText }}</span>
                    <i class="fas fa-arrow-right text-[10px] transition-transform group-hover:translate-x-1"></i>
                </a>
            </div>
        </div>
    </div>
</section>

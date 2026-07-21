@props(['config' => []])
@php
    $badge = $config['badge'] ?? 'Island-Wide Delivery';
    $title = $config['title'] ?? 'Quality Smartphone Displays & Parts Across Sri Lanka';
    $text = $config['text'] ?? 'Genuine smartphone display assemblies and electronics parts delivered to your doorstep. Track your order in real time.';
    $buttonText = $config['button_text'] ?? 'Shop Now';
    $buttonLink = $config['button_link'] ?? '/products';
    $bgFrom = $config['bg_from'] ?? '#0f172a';
    $bgTo = $config['bg_to'] ?? '#334155';
@endphp

<section class="mx-auto mt-8 max-w-7xl">
    <div class="card rounded-[2rem] px-6 py-6 text-white sm:px-8" style="background:linear-gradient(120deg, {{ $bgFrom }}, {{ $bgTo }})">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="max-w-3xl">
                <span class="inline-flex rounded-full bg-white/15 px-3 py-1 text-[11px] font-bold uppercase tracking-[0.2em]">{{ $badge }}</span>
                <h2 class="mt-4 text-2xl font-black sm:text-3xl">{{ $title }}</h2>
                <p class="mt-3 max-w-2xl text-sm leading-7 text-white/80">{{ $text }}</p>
            </div>
            <a wire:navigate href="{{ url($buttonLink) }}" class="inline-flex items-center gap-2 rounded-full bg-white px-6 py-3 text-sm font-bold text-slate-900 hover:scale-105 transition-transform">
                {{ $buttonText }}
                <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>
    </div>
</section>

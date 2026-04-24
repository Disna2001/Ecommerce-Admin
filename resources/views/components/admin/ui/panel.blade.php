@props([
    'title' => null,
    'description' => null,
    'eyebrow' => null,
    'padding' => 'p-5',
    'bodyClass' => '',
])

<section {{ $attributes->class([
    'admin-surface rounded-[2rem] border border-white/60 bg-white/90 shadow-[0_18px_60px_rgba(15,23,42,0.08)] dark:border-white/10 dark:bg-slate-950/75',
]) }}>
    @if($title || $description || $eyebrow || isset($header))
        <div class="{{ $padding }} {{ $bodyClass }}">
            @isset($header)
                {{ $header }}
            @else
                @if($eyebrow)
                    <p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-400 dark:text-slate-500">{{ $eyebrow }}</p>
                @endif

                @if($title)
                    <h3 class="mt-1 text-lg font-bold text-slate-900 dark:text-white">{{ $title }}</h3>
                @endif

                @if($description)
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $description }}</p>
                @endif
            @endisset
        </div>
    @endif

    <div @class([$padding, $bodyClass, 'pt-0' => $title || $description || $eyebrow || isset($header)])>
        {{ $slot }}
    </div>
</section>

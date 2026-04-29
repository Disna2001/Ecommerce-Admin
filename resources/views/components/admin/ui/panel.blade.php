@props([
    'title' => null,
    'description' => null,
    'eyebrow' => null,
    'padding' => 'p-5',
    'bodyClass' => '',
])

<section {{ $attributes->class([
    'admin-surface rounded-2xl border border-slate-200/80 bg-white/78 shadow-sm dark:border-slate-800 dark:bg-slate-950/58',
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

@props([
    'label',
    'value',
    'icon' => null,
    'tone' => 'indigo',
    'category' => null,
    'status' => null,
    'href' => null,
])

@php
    $tag = $href ? 'a' : 'div';
    $toneMap = [
        'indigo' => 'bg-indigo-50 text-indigo-600',
        'emerald' => 'bg-emerald-50 text-emerald-600',
        'amber' => 'bg-amber-50 text-amber-600',
        'rose' => 'bg-rose-50 text-rose-600',
        'slate' => 'bg-slate-100 text-slate-600',
    ];
    $badgeStyle = $toneMap[$tone] ?? $toneMap['indigo'];
@endphp

<{{ $tag }} 
    @if($href) href="{{ $href }}" @endif 
    class="block rounded-xl border border-slate-200 bg-white p-6 shadow-sm transition-all duration-200 {{ $href ? 'hover:border-slate-300 hover:shadow-md' : '' }}"
>
    <div class="flex items-center justify-between mb-4">
        @if($icon)
            <div class="flex h-10 w-10 items-center justify-center rounded-lg {{ $badgeStyle }}">
                <i class="fas {{ $icon }} text-sm"></i>
            </div>
        @endif

        @if($category)
            <span class="inline-flex items-center rounded-md px-2.5 py-1 text-[11px] font-semibold uppercase tracking-wider {{ $badgeStyle }}">
                {{ $category }}
            </span>
        @elseif($status)
            <div class="flex items-center gap-1.5">
                <span class="h-2 w-2 rounded-full {{ in_array($status, ['green', 'emerald']) ? 'bg-emerald-500' : (in_array($status, ['red', 'rose']) ? 'bg-rose-500' : 'bg-amber-500') }}"></span>
            </div>
        @endif
    </div>

    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">{{ $label }}</p>
    <h3 class="text-2xl font-bold tracking-tight text-slate-900">{{ $value }}</h3>
</{{ $tag }}>

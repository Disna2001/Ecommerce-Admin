@props([
    'label',
    'value',
    'hint' => null,
    'tone' => 'slate',
    'active' => false,
])

@php
    $tones = [
        'slate' => 'border-white/70 bg-white/80 text-slate-900 dark:border-white/10 dark:bg-slate-900/70 dark:text-white',
        'blue' => 'border-white/70 bg-white/80 text-slate-900 dark:border-white/10 dark:bg-slate-900/70 dark:text-white',
        'emerald' => 'border-white/70 bg-white/80 text-slate-900 dark:border-white/10 dark:bg-slate-900/70 dark:text-white',
        'amber' => 'border-white/70 bg-white/80 text-slate-900 dark:border-white/10 dark:bg-slate-900/70 dark:text-white',
        'accent' => 'border-white/70 bg-gradient-to-br from-indigo-500 via-fuchsia-500 to-amber-400 text-white shadow-lg shadow-indigo-500/20 dark:border-white/10',
    ];
@endphp

<div {{ $attributes->class([
    'rounded-3xl border p-4 text-left shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg',
    $tones[$tone] ?? $tones['slate'],
    'ring-2 ring-blue-300/60 dark:ring-blue-500/40' => $active,
]) }}>
    <p class="text-xs font-semibold uppercase tracking-[0.35em] {{ $tone === 'accent' ? 'text-white/70' : 'text-slate-400 dark:text-slate-500' }}">{{ $label }}</p>
    <p class="mt-3 text-3xl font-black">{{ $value }}</p>
    @if($hint)
        <p class="mt-2 text-sm {{ $tone === 'accent' ? 'text-white/80' : 'text-slate-500 dark:text-slate-400' }}">{{ $hint }}</p>
    @endif
</div>

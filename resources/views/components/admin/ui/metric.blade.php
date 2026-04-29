@props([
    'label',
    'value',
    'hint' => null,
    'tone' => 'slate',
    'active' => false,
])

@php
    $tones = [
        'slate' => 'border-slate-200 bg-slate-50/85 text-slate-900 dark:border-slate-800 dark:bg-slate-900/60 dark:text-white',
        'blue' => 'border-sky-200 bg-sky-50/80 text-slate-900 dark:border-sky-900/60 dark:bg-sky-950/30 dark:text-white',
        'emerald' => 'border-emerald-200 bg-emerald-50/80 text-slate-900 dark:border-emerald-900/60 dark:bg-emerald-950/30 dark:text-white',
        'amber' => 'border-amber-200 bg-amber-50/80 text-slate-900 dark:border-amber-900/60 dark:bg-amber-950/30 dark:text-white',
        'accent' => 'border-indigo-200 bg-gradient-to-r from-indigo-500 via-fuchsia-500 to-violet-500 text-white shadow-sm dark:border-indigo-500/40',
    ];
@endphp

<div {{ $attributes->class([
    'rounded-2xl border p-4 text-left transition',
    $tones[$tone] ?? $tones['slate'],
    'ring-2 ring-blue-300/60 dark:ring-blue-500/40' => $active,
]) }}>
    <p class="text-xs font-semibold uppercase tracking-[0.35em] {{ $tone === 'accent' ? 'text-white/70' : 'text-slate-400 dark:text-slate-500' }}">{{ $label }}</p>
    <p class="mt-2 text-2xl font-black">{{ $value }}</p>
    @if($hint)
        <p class="mt-1.5 text-sm {{ $tone === 'accent' ? 'text-white/80' : 'text-slate-500 dark:text-slate-400' }}">{{ $hint }}</p>
    @endif
</div>

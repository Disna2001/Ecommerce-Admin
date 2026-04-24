@props([
    'title',
    'description',
])

<div {{ $attributes->class(['mx-auto max-w-md space-y-2 text-center']) }}>
    <p class="text-lg font-semibold text-slate-900 dark:text-white">{{ $title }}</p>
    <p class="text-sm text-slate-500 dark:text-slate-400">{{ $description }}</p>
</div>

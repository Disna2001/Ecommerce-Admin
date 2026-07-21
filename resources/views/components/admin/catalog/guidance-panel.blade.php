@props([
    'title' => 'Catalog Guidance',
    'tip',
    'icon' => 'fa-lightbulb'
])

<div class="rounded-xl border border-slate-200 bg-white p-6 shadow-xs">
    <div class="flex items-start gap-4">
        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 flex-shrink-0">
            <i class="fas {{ $icon }} text-sm"></i>
        </div>
        <div>
            <h3 class="text-sm font-bold text-slate-900">{{ $title }}</h3>
            <p class="mt-1 text-xs text-slate-500 leading-relaxed">{{ $tip }}</p>
        </div>
    </div>
</div>

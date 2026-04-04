<aside class="admin-surface rounded-[2rem] border border-white/60 bg-white/90 p-4 shadow-[0_18px_60px_rgba(15,23,42,0.08)] dark:border-white/10 dark:bg-slate-950/75">
    <p class="px-2 text-xs font-semibold uppercase tracking-[0.25em] text-slate-400 dark:text-slate-500">Sections</p>
    <div class="mt-3 space-y-2">
        @foreach([
            'branding' => ['Branding', 'fa-store'],
            'homepage' => ['Homepage', 'fa-house'],
            'colors' => ['Colors', 'fa-palette'],
            'hero' => ['Hero', 'fa-image'],
            'sections' => ['Sections', 'fa-layer-group'],
            'topbar' => ['Top Bar', 'fa-bars'],
            'detail' => ['Detail Page', 'fa-box-open'],
            'payment' => ['Payments', 'fa-credit-card'],
            'categories' => ['Categories', 'fa-table-cells-large'],
            'navigation' => ['Navigation', 'fa-compass'],
            'footer' => ['Footer', 'fa-sitemap'],
        ] as $tab => [$label, $icon])
            <button wire:click="$set('activeTab', '{{ $tab }}')" @class(['flex w-full items-center justify-between gap-3 rounded-2xl px-4 py-3 text-left text-sm font-medium transition', 'bg-slate-900 text-white shadow-md shadow-slate-900/10 dark:bg-white dark:text-slate-900' => $activeTab === $tab, 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-900 dark:hover:text-white' => $activeTab !== $tab])><span class="flex items-center gap-3"><i class="fas {{ $icon }} w-4 text-center"></i><span>{{ $label }}</span></span><span class="text-[11px] font-semibold uppercase tracking-[0.2em] {{ $activeTab === $tab ? 'text-white/70 dark:text-slate-500' : 'text-slate-400 dark:text-slate-500' }}">{{ $tabStats[$tab] ?? '' }}</span></button>
        @endforeach
    </div>
    <div class="mt-6 rounded-2xl border border-dashed border-slate-200 p-4 dark:border-slate-800"><p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">Operator Notes</p><ul class="mt-3 space-y-2 text-sm leading-6 text-slate-500 dark:text-slate-400"><li>Save once after editing across multiple tabs.</li><li>Use Display Items for homepage product rails.</li><li>Remove old assets here to keep branding clean.</li></ul></div>
</aside>

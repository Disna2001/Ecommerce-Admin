@props(['title', 'breadcrumb' => 'Overview'])

@php
    $siteName = \App\Models\SiteSetting::get('site_name', config('app.name', 'Display Lanka'));
    $normalizedTitle = \Illuminate\Support\Str::lower(trim($title));
    $titleAliases = [
        'order management' => 'orders',
        'stock management' => 'stocks',
        'brand management' => 'brands',
        'category management' => 'categories',
        'item type management' => 'item types',
        'make management' => 'makes',
        'supplier management' => 'suppliers',
        'warranty management' => 'warranties',
        'invoice management' => 'invoices',
        'point of sale' => 'pos',
        'site management' => 'settings',
        'system settings' => 'settings',
        'quality levels' => 'item quality levels',
        'item quality levels' => 'item quality levels',
    ];
    $headerIcons = [
        'dashboard' => [
            'bg' => 'rgba(79, 70, 229, 0.12)',
            'color' => '#4f46e5',
            'svg' => 'M3.75 3.75h6.75v6.75H3.75V3.75zM13.5 3.75h6.75v4.5H13.5v-4.5zM13.5 11.25h6.75v9H13.5v-9zM3.75 13.5h6.75v6.75H3.75V13.5z',
        ],
        'orders' => [
            'bg' => 'rgba(245, 158, 11, 0.12)',
            'color' => '#f59e0b',
            'svg' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        ],
        'stocks' => [
            'bg' => 'rgba(16, 185, 129, 0.12)',
            'color' => '#10b981',
            'svg' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
        ],
        'categories' => [
            'bg' => 'rgba(6, 182, 212, 0.12)',
            'color' => '#06b6d4',
            'svg' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10',
        ],
        'brands' => [
            'bg' => 'rgba(236, 72, 153, 0.12)',
            'color' => '#ec4899',
            'svg' => 'M16 4v12l-4-2-4 2V4M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z',
        ],
        'item types' => [
            'bg' => 'rgba(168, 85, 247, 0.12)',
            'color' => '#a855f7',
            'svg' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l5 5a2 2 0 01.586 1.414V19a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z',
        ],
        'item quality levels' => [
            'bg' => 'rgba(234, 179, 8, 0.12)',
            'color' => '#eab308',
            'svg' => 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z',
        ],
        'makes' => [
            'bg' => 'rgba(59, 130, 246, 0.12)',
            'color' => '#3b82f6',
            'svg' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
        ],
        'suppliers' => [
            'bg' => 'rgba(249, 115, 22, 0.12)',
            'color' => '#f97316',
            'svg' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
        ],
        'warranties' => [
            'bg' => 'rgba(34, 197, 94, 0.12)',
            'color' => '#22c55e',
            'svg' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
        ],
        'invoices' => [
            'bg' => 'rgba(14, 165, 233, 0.12)',
            'color' => '#0ea5e9',
            'svg' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        ],
        'pos' => [
            'bg' => 'rgba(8, 145, 178, 0.12)',
            'color' => '#0891b2',
            'svg' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z',
        ],
        'users' => [
            'bg' => 'rgba(99, 102, 241, 0.12)',
            'color' => '#6366f1',
            'svg' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
        ],
        'roles' => [
            'bg' => 'rgba(168, 85, 247, 0.12)',
            'color' => '#a855f7',
            'svg' => 'M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z',
        ],
        'settings' => [
            'bg' => 'rgba(148, 163, 184, 0.16)',
            'color' => '#475569',
            'svg' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z',
        ],
        'activity logs' => [
            'bg' => 'rgba(244, 63, 94, 0.12)',
            'color' => '#e11d48',
            'svg' => 'M3.75 12h3l2.25-6 4.5 12 2.25-6h4.5',
        ],
        'notification outbox' => [
            'bg' => 'rgba(14, 165, 233, 0.12)',
            'color' => '#0ea5e9',
            'svg' => 'M3.75 6.75h16.5v10.5H3.75V6.75zm0 0L12 13.5l8.25-6.75',
        ],
        'stock movements' => [
            'bg' => 'rgba(34, 197, 94, 0.12)',
            'color' => '#22c55e',
            'svg' => 'M7.5 7.5h9m-9 4.5h6m-6 4.5h9M4.5 4.5h15a1.5 1.5 0 011.5 1.5v12A1.5 1.5 0 0119.5 19.5h-15A1.5 1.5 0 013 18V6a1.5 1.5 0 011.5-1.5z',
        ],
        'system health' => [
            'bg' => 'rgba(14, 165, 233, 0.12)',
            'color' => '#0284c7',
            'svg' => 'M11.25 3.75h1.5m-1.5 16.5h1.5M3.75 11.25v1.5m16.5-1.5v1.5M6.22 6.22l1.06 1.06m9.44 9.44l1.06 1.06m0-11.56l-1.06 1.06m-9.44 9.44L6.22 17.78M12 8.25A3.75 3.75 0 1012 15.75 3.75 3.75 0 0012 8.25zm0-5.25a9 9 0 110 18 9 9 0 010-18z',
        ],
        'default' => [
            'bg' => 'rgba(79, 70, 229, 0.12)',
            'color' => '#4f46e5',
            'svg' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        ],
    ];
    $resolvedTitle = $titleAliases[$normalizedTitle] ?? $normalizedTitle;
    $headerIcon = $headerIcons[$resolvedTitle] ?? $headerIcons['default'];
@endphp

<div class="admin-header-card" style="margin-bottom:0.9rem;padding:1rem 1.15rem;">
    <div style="display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;">
        <div style="display:flex;align-items:flex-start;gap:1rem;">
            <span style="display:inline-flex;align-items:center;justify-content:center;width:3rem;height:3rem;border-radius:1rem;background:{{ $headerIcon['bg'] }};color:{{ $headerIcon['color'] }};flex-shrink:0;">
                <svg style="width:1.2rem;height:1.2rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $headerIcon['svg'] }}"></path>
                </svg>
            </span>
            <div>
            <div style="display:flex;align-items:center;gap:0.55rem;flex-wrap:wrap;font-size:0.78rem;font-weight:700;letter-spacing:0.16em;text-transform:uppercase;color:var(--admin-text-soft);">
                <span>{{ $siteName }}</span>
                <span>&bull;</span>
                <span>{{ $breadcrumb }}</span>
            </div>
            <h1 style="margin-top:0.5rem;font-size:clamp(1.55rem,2vw,2.15rem);font-weight:800;color:var(--admin-text);line-height:1.1;">{{ $title }}</h1>
            </div>
        </div>

        <div style="display:flex;align-items:center;gap:0.6rem;flex-wrap:wrap;">
            <span class="admin-chip">
                <span style="width:0.5rem;height:0.5rem;border-radius:999px;background:var(--admin-success);"></span>
                Admin online
            </span>
            @if(isset($actions))
                <div style="display:flex;align-items:center;gap:0.75rem;flex-wrap:wrap;">
                    {{ $actions }}
                </div>
            @endif
        </div>
    </div>
</div>

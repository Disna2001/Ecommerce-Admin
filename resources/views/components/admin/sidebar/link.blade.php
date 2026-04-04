@props(['href', 'route', 'icon', 'badge' => null, 'description' => null])

@php
$icons = [
    'dashboard'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />',
    'orders'     => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.25 6.75h12m-12 4.5h12m-12 4.5h7.5M3.75 5.25A1.5 1.5 0 015.25 3.75h13.5a1.5 1.5 0 011.5 1.5v13.5a1.5 1.5 0 01-1.5 1.5H5.25a1.5 1.5 0 01-1.5-1.5V5.25zm1.5 0v13.5" />',
    'stock'      => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />',
    'categories' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />',
    'brands'     => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 4v12l-4-2-4 2V4M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />',
    'item-types' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l5 5a2 2 0 01.586 1.414V19a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z" />',
    'quality'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />',
    'make'       => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />',
    'suppliers'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />',
    'warranties' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />',
    'users'      => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />',
    'tenants'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 6.75h16.5M6 3.75v6m12-6v6M5.25 20.25h13.5A1.5 1.5 0 0020.25 18.75v-9A1.5 1.5 0 0018.75 8.25H5.25a1.5 1.5 0 00-1.5 1.5v9a1.5 1.5 0 001.5 1.5z" />',
    'products'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.5 7.5 12 3l7.5 4.5M4.5 7.5V16.5L12 21m-7.5-13.5L12 12m7.5-4.5V16.5L12 21m0-9v9" />',
    'clients'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 6.75a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 19.5a7.5 7.5 0 0115 0" />',
    'deployments'=> '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 6.75h16.5v10.5H3.75V6.75zm3 13.5h10.5M9.75 17.25v3M14.25 17.25v3" />',
    'roles'      => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />',
    'settings'   => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />',
    'appearance' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.5 7.5h15m-15 0A1.5 1.5 0 003 9v10.5A1.5 1.5 0 004.5 21h15a1.5 1.5 0 001.5-1.5V9A1.5 1.5 0 0019.5 7.5m-15 0L6 4.875A1.5 1.5 0 017.308 4h9.384A1.5 1.5 0 0118 4.875L19.5 7.5M9 13.5l2.25 2.25L15 12" />',
    'invoice'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />',
    'pos'        => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />',
    'review'     => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />',
    'storefront' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10.5l1.894-4.736A1.5 1.5 0 016.287 4.5h11.426a1.5 1.5 0 011.393.964L21 10.5M4.5 9.75h15M6 10.5v7.5a1.5 1.5 0 001.5 1.5h9a1.5 1.5 0 001.5-1.5v-7.5M9 15h6" />',
    'spark'      => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 3l1.8 5.2L19 10l-5.2 1.8L12 17l-1.8-5.2L5 10l5.2-1.8L12 3z" />',
    'bolt'       => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 2L4 14h6l-1 8 9-12h-6l1-8z" />',
    'display'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.5 5.25A2.25 2.25 0 016.75 3h10.5a2.25 2.25 0 012.25 2.25v13.5A2.25 2.25 0 0117.25 21H6.75a2.25 2.25 0 01-2.25-2.25V5.25zM8.25 7.5h7.5M8.25 12h7.5M8.25 16.5h4.5" />',
    'activity-logs' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 12h3l2.25-6 4.5 12 2.25-6h4.5M4.5 4.5v15m15-15v15" />',
    'notification-outbox' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 6.75h16.5v10.5H3.75V6.75zm0 0L12 13.5l8.25-6.75" />',
    'stock-movements' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7.5 7.5h9m-9 4.5h6m-6 4.5h9M4.5 4.5h15a1.5 1.5 0 011.5 1.5v12A1.5 1.5 0 0119.5 19.5h-15A1.5 1.5 0 013 18V6a1.5 1.5 0 011.5-1.5z" />',
    'system-health' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.25 3.75h1.5m-1.5 16.5h1.5M3.75 11.25v1.5m16.5-1.5v1.5M6.22 6.22l1.06 1.06m9.44 9.44l1.06 1.06m0-11.56l-1.06 1.06m-9.44 9.44L6.22 17.78M12 8.25A3.75 3.75 0 1012 15.75 3.75 3.75 0 0012 8.25zm0-5.25a9 9 0 110 18 9 9 0 010-18z" />',
    'system'     => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.5 9.75h15m-15 4.5h15M6.75 5.25h.008v.008H6.75V5.25zm0 4.5h.008v.008H6.75v-.008zm0 4.5h.008v.008H6.75v-.008z" />',
    'default'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
];
@endphp

<a href="{{ $href }}" wire:navigate
   class="nav-link {{ request()->routeIs($route) ? 'active' : '' }}">
    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        {!! $icons[$icon] ?? $icons['default'] !!}
    </svg>
    <span class="nav-link__body">
        <span class="nav-link__label">{{ $slot }}</span>
        @if($description)
            <span class="nav-link__description">{{ $description }}</span>
        @endif
    </span>
    @if($badge)
        <span class="nav-link__badge">{{ $badge }}</span>
    @endif
</a>

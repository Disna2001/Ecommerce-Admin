@php
    $siteName = \App\Models\SiteSetting::get('site_name', config('app.name', 'Display Lanka'));
    $logoPath = \App\Models\SiteSetting::get('logo_path', '');
@endphp

<a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 no-underline" style="min-width:0;">
    @if (!empty($logoPath))
        <div style="width:2.85rem;height:2.85rem;border-radius:1rem;background:rgba(255,255,255,0.12);display:flex;align-items:center;justify-content:center;padding:0.35rem;flex-shrink:0;">
            <img src="{{ \Illuminate\Support\Facades\Storage::url($logoPath) }}" alt="{{ $siteName }}" class="h-full w-full object-contain">
        </div>
    @else
        <div style="width:2.85rem;height:2.85rem;background:linear-gradient(135deg,#7c3aed,#4f46e5,#06b6d4);border-radius:1rem;display:flex;align-items:center;justify-content:center;box-shadow:0 18px 34px -18px rgba(99,102,241,0.72);flex-shrink:0;">
            <span style="color:white;font-weight:800;font-size:0.92rem;">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($siteName, 0, 2)) }}</span>
        </div>
    @endif

    <span class="logo-text desktop-only">
        <span>{{ $siteName }}</span>
        <span class="logo-subtext">Operations Console</span>
    </span>
</a>

@php
    $siteName = \App\Models\SiteSetting::get('site_name', config('app.name', 'Display Lanka'));
    $logoPath = \App\Models\SiteSetting::get('logo_path', '');
@endphp

@if (!empty($logoPath))
    <img
        src="{{ \Illuminate\Support\Facades\Storage::url($logoPath) }}"
        alt="{{ $siteName }}"
        {{ $attributes->merge(['class' => 'object-contain']) }}
    >
@else
    <div {{ $attributes->merge(['class' => 'inline-flex items-center justify-center rounded-2xl bg-slate-900 px-4 py-2 text-sm font-bold tracking-[0.2em] text-white']) }}>
        {{ strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $siteName), 0, 2) ?: 'DL') }}
    </div>
@endif

@php
    $siteName = \App\Models\SiteSetting::get('site_name', config('app.name', 'Display Lanka'));
    $logoPath = \App\Models\SiteSetting::get('logo_path', '');
@endphp

<a href="{{ route('admin.dashboard') }}" class="flex items-center gap-4 group no-underline">
    <div class="relative">
        <div class="absolute -inset-2 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl blur opacity-20 group-hover:opacity-40 transition duration-1000 group-hover:duration-200"></div>
        @if (!empty($logoPath))
            <div class="relative h-11 w-11 rounded-xl bg-white dark:bg-slate-800 p-2 shadow-2xl border border-white/10 flex items-center justify-center overflow-hidden">
                <img src="{{ \Illuminate\Support\Facades\Storage::url($logoPath) }}" alt="{{ $siteName }}" class="h-full w-full object-contain">
            </div>
        @else
            <div class="relative h-11 w-11 rounded-xl bg-slate-900 flex items-center justify-center shadow-2xl border border-white/10 overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/20 to-purple-500/20"></div>
                <span class="relative text-[10px] font-black text-white tracking-widest">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($siteName, 0, 2)) }}</span>
            </div>
        @endif
    </div>

    <div class="hidden lg:block">
        <h2 class="text-sm font-black tracking-tight text-white uppercase">{{ $siteName }}</h2>
        <div class="flex items-center gap-2 mt-0.5">
            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
            <p class="text-[9px] font-black text-white/40 uppercase tracking-[0.2em] leading-none">Admin Console</p>
        </div>
    </div>
</a>

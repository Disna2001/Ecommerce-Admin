@props(['href', 'route', 'icon', 'badge' => null])

@php
    $isActive = request()->routeIs($route . '*');
@endphp

<a href="{{ $href }}" wire:navigate 
    class="group relative flex items-center gap-4 px-4 py-3.5 rounded-2xl transition-all duration-300 {{ $isActive ? 'bg-slate-900 text-white shadow-xl shadow-slate-200 dark:shadow-none' : 'text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}"
>
    <!-- Active Indicator Dot -->
    @if($isActive)
        <div class="absolute left-0 top-1/2 -translate-y-1/2 h-5 w-1.5 bg-indigo-500 rounded-r-full animate-in slide-in-from-left-full duration-500"></div>
    @endif

    <div class="flex h-6 w-6 items-center justify-center flex-shrink-0 transition-transform group-hover:scale-110">
        <i class="fas {{ $icon }} text-[13px] {{ $isActive ? 'text-indigo-400' : 'opacity-40 group-hover:opacity-100' }}"></i>
    </div>

    <span class="text-[11px] font-black uppercase tracking-[0.15em] flex-1 truncate">
        {{ $slot }}
    </span>

    @if($badge)
        <div class="px-2 py-0.5 rounded-lg text-[9px] font-black {{ $isActive ? 'bg-white/10 text-white' : 'bg-rose-100 text-rose-600' }} animate-pulse">
            {{ $badge }}
        </div>
    @endif
</a>

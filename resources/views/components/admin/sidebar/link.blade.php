@props(['href', 'route', 'icon', 'badge' => null])

@php
    $isActive = request()->routeIs($route . '*');
@endphp

<a href="{{ $href }}" wire:navigate 
    class="group relative flex items-center gap-2.5 px-2.5 py-1.5 rounded-md transition-colors duration-150 {{ $isActive ? 'bg-slate-900 text-white dark:bg-indigo-600 dark:text-white' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/80 hover:text-slate-900 dark:hover:text-white' }}"
>
    <!-- Active Indicator Bar -->
    @if($isActive)
        <div class="absolute left-0 top-1/2 -translate-y-1/2 h-3.5 w-1 bg-indigo-500 dark:bg-white rounded-r-full"></div>
    @endif

    <i class="fas {{ $icon }} w-4 text-center text-xs flex-shrink-0 opacity-80 group-hover:opacity-100"></i>

    <span class="text-xs font-medium tracking-normal flex-1 truncate">
        {{ $slot }}
    </span>

    @if($badge)
        <span class="px-1.5 py-0.2 rounded-full text-[10px] font-semibold {{ $isActive ? 'bg-white/20 text-white' : 'bg-rose-500 text-white' }}">
            {{ $badge }}
        </span>
    @endif
</a>

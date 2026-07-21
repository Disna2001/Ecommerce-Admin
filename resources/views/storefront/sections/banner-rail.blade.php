@props(['config' => []])
@php
    $title = $config['title'] ?? 'Promotional Highlights';
    $items = $config['items'] ?? [];
@endphp

<section class="py-8 my-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if(!empty($title))
            <h2 class="text-2xl font-black text-slate-900 dark:text-white mb-6">{{ $title }}</h2>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($items as $item)
                <div class="group relative overflow-hidden rounded-2xl bg-white dark:bg-slate-800 p-6 border border-slate-200/80 dark:border-slate-700/80 shadow-sm hover:shadow-md transition-all">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="font-bold text-lg text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">{{ $item['title'] ?? '' }}</h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ $item['subtitle'] ?? '' }}</p>
                        </div>
                        @if(!empty($item['image']))
                            <img src="{{ $item['image'] }}" class="h-12 w-12 rounded-xl object-cover" alt="" />
                        @endif
                    </div>
                    @if(!empty($item['link']))
                        <a href="{{ $item['link'] }}" class="mt-4 inline-flex items-center gap-1.5 text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline">
                            <span>{{ data_get($item, 'link_label', 'Explore Deal') }}</span>
                            <i class="fas fa-chevron-right text-[10px]"></i>
                        </a>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>

@props(['attentionItems'])

@if($attentionItems->count() > 0)
    <div class="rounded-xl border border-amber-200 bg-amber-50/40 p-6 shadow-sm">
        <div class="flex items-center gap-3 mb-5 pb-3 border-b border-amber-200/60">
            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-amber-500 text-white font-semibold shadow-xs">
                <i class="fas fa-triangle-exclamation text-xs"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-slate-900">Attention Needed</h3>
                <p class="text-xs text-amber-800 font-medium">Items requiring administrative action</p>
            </div>
        </div>

        <div class="grid gap-3 sm:grid-cols-2">
            @foreach($attentionItems as $item)
                <a href="{{ $item['route'] }}" class="flex items-center justify-between p-4 rounded-lg bg-white border border-amber-200/80 hover:border-amber-300 hover:shadow-xs transition-all duration-150 group">
                    <div class="flex items-center gap-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-amber-100 text-amber-700 font-semibold group-hover:scale-105 transition-transform">
                            <i class="fas {{ $item['icon'] }} text-xs"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-900">{{ $item['title'] }}</p>
                            <p class="text-[11px] font-medium text-slate-500 mt-0.5">Action required</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full bg-amber-500 text-xs font-bold text-white shadow-xs">
                        {{ $item['count'] }}
                    </span>
                </a>
            @endforeach
        </div>
    </div>
@endif

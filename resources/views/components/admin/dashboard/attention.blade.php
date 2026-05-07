@props(['attentionItems'])

@if($attentionItems->count() > 0)
    <div class="rounded-[2.5rem] border border-slate-900 bg-slate-900 p-8 shadow-2xl relative overflow-hidden group">
        <div class="absolute right-0 top-0 -mr-24 -mt-24 h-80 w-80 rounded-full bg-white/5 group-hover:scale-110 transition-transform duration-1000"></div>
        
        <div class="relative z-10 flex items-center gap-4 mb-8">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-rose-500 text-white animate-pulse">
                <i class="fas fa-tower-broadcast text-xs"></i>
            </div>
            <div>
                <h3 class="text-sm font-black text-white uppercase tracking-widest">Protocol Alerts</h3>
                <p class="text-[9px] font-bold text-white/40 uppercase tracking-widest mt-1">Immediate Administrative Attention Required</p>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 relative z-10">
            @foreach($attentionItems as $item)
                <a href="{{ $item['route'] }}" class="flex items-center justify-between p-5 rounded-2xl bg-white/5 border border-white/10 hover:bg-white/10 hover:border-white/20 transition-all group/item">
                    <div class="flex items-center gap-4">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-{{ $item['tone'] }}-500/20 text-{{ $item['tone'] }}-400 group-hover/item:scale-110 transition-transform">
                            <i class="fas {{ $item['icon'] }} text-xs"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-white uppercase tracking-widest">{{ $item['title'] }}</p>
                            <p class="text-[9px] font-bold text-white/40 uppercase tracking-tighter mt-0.5">Registry Entry Required</p>
                        </div>
                    </div>
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-{{ $item['tone'] }}-500 text-white shadow-lg">
                        <span class="text-xs font-black">{{ $item['count'] }}</span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
@endif

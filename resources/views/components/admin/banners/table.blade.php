<div class="space-y-6">
    @foreach($positions as $posKey => $posLabel)
        @php $posBanners = $banners->where('position', $posKey); @endphp
        
        @if($posBanners->count() > 0)
            <div class="space-y-4">
                <div class="flex items-center gap-3 px-4">
                    <div class="h-1.5 w-1.5 rounded-full bg-slate-900"></div>
                    <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-900">{{ $posLabel }} Queue</h3>
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">{{ $posBanners->count() }} active items</span>
                </div>

                <div class="grid gap-4">
                    @foreach($posBanners as $banner)
                        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm group hover:border-slate-900 transition-all">
                            <div class="flex flex-col gap-6 lg:flex-row lg:items-center">
                                <!-- Visual Preview -->
                                <div class="relative h-24 w-40 flex-shrink-0 overflow-hidden rounded-2xl border border-slate-100 bg-slate-50 shadow-inner group-hover:scale-[1.02] transition-transform">
                                    @if($banner->image_path)
                                        <img src="{{ Storage::url($banner->image_path) }}" class="h-full w-full object-cover">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center text-slate-200" style="background: {{ $banner->bg_color }}">
                                            <i class="fas fa-image text-xl"></i>
                                        </div>
                                    @endif
                                    <div class="absolute inset-0 bg-slate-900/0 group-hover:bg-slate-900/10 transition-colors"></div>
                                    <div class="absolute bottom-2 left-2 flex gap-1">
                                         <div class="h-2 w-2 rounded-full {{ $banner->isLive() ? 'bg-emerald-500 animate-pulse' : 'bg-slate-300' }}"></div>
                                    </div>
                                </div>

                                <!-- Campaign Context -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between mb-1">
                                        <div class="flex items-center gap-2">
                                            <span class="text-[9px] font-black uppercase tracking-widest px-2 py-0.5 rounded-md bg-slate-100 text-slate-500">{{ $banner->position }}</span>
                                            @if($banner->starts_at || $banner->ends_at)
                                                <span class="text-[9px] font-bold text-sky-600 uppercase tracking-tight">
                                                    <i class="fas fa-clock text-[8px] mr-1"></i>
                                                    Scheduled
                                                </span>
                                            @endif
                                        </div>
                                        <span class="text-[9px] font-mono font-bold text-slate-400">#{{ $banner->id }}</span>
                                    </div>
                                    <h4 class="text-sm font-black text-slate-900 truncate tracking-tight">{{ $banner->title }}</h4>
                                    <p class="mt-1 text-[10px] font-bold text-slate-400 uppercase tracking-tight line-clamp-1">{{ $banner->caption ?: $banner->subtitle }}</p>
                                    
                                    <div class="mt-4 flex flex-wrap items-center gap-3">
                                        <div class="flex items-center gap-2 rounded-lg bg-slate-50 px-2.5 py-1.5 border border-slate-100">
                                            <div class="h-2 w-2 rounded-full" style="background-color: {{ $banner->bg_color }}"></div>
                                            <span class="text-[9px] font-black text-slate-900 uppercase tracking-widest">Theme Palette</span>
                                        </div>
                                        @if($banner->button_text)
                                            <div class="flex items-center gap-2 rounded-lg bg-slate-50 px-2.5 py-1.5 border border-slate-100">
                                                <i class="fas fa-link text-[8px] text-slate-400"></i>
                                                <span class="text-[9px] font-black text-slate-900 uppercase tracking-widest">{{ $banner->button_text }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Action Workspace -->
                                <div class="flex items-center gap-3 lg:border-l lg:border-slate-100 lg:pl-6">
                                    <div class="flex flex-col gap-1">
                                        <button wire:click="moveUp({{ $banner->id }})" class="p-1.5 text-slate-400 hover:text-slate-900 transition-colors"><i class="fas fa-chevron-up text-[10px]"></i></button>
                                        <button wire:click="moveDown({{ $banner->id }})" class="p-1.5 text-slate-400 hover:text-slate-900 transition-colors"><i class="fas fa-chevron-down text-[10px]"></i></button>
                                    </div>
                                    
                                    <button 
                                        wire:click="toggleActive({{ $banner->id }})"
                                        class="relative inline-flex h-7 w-14 items-center rounded-full transition-colors {{ $banner->is_active ? 'bg-emerald-500' : 'bg-slate-200' }}"
                                    >
                                        <span class="inline-block h-5 w-5 transform rounded-full bg-white transition-transform {{ $banner->is_active ? 'translate-x-8' : 'translate-x-1' }}"></span>
                                    </button>

                                    <div class="flex items-center gap-2">
                                        <button wire:click="edit({{ $banner->id }})" class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-900 hover:text-white transition-all shadow-sm">
                                            <i class="fas fa-pencil text-xs"></i>
                                        </button>
                                        <button 
                                            onclick="confirm('Decommission this campaign?') || event.stopImmediatePropagation()"
                                            wire:click="delete({{ $banner->id }})" 
                                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-rose-50 text-rose-500 hover:bg-rose-500 hover:text-white transition-all shadow-sm"
                                        >
                                            <i class="fas fa-trash-alt text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endforeach

    @if($banners->count() === 0)
        <div class="py-20 text-center">
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-[2rem] bg-white text-slate-200 shadow-sm mb-6">
                <i class="fas fa-panorama text-3xl"></i>
            </div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-relaxed">No campaign assets found.<br>Deploy your first banner to activate storefront marketing.</p>
        </div>
    @endif
</div>

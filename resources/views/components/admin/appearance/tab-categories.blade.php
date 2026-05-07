@php $cats = \App\Models\Category::all(); @endphp

<div class="space-y-8">
    <div class="flex items-center gap-4">
        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-amber-600 shadow-inner">
            <i class="fas fa-tags text-lg"></i>
        </div>
        <div>
            <h3 class="text-xl font-black text-slate-900 tracking-tight">Discovery Taxonomy</h3>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Category Strips & Catalog Heroes</p>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[1fr_400px]">
        <div class="space-y-8">
            <!-- Homepage Strip Configuration -->
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-6">Homepage Discovery Strip</p>
                <div class="grid gap-6 md:grid-cols-2">
                    <div class="group">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Strip Headline</label>
                        <input type="text" wire:model="category_strip_title" class="mt-2 w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-xs font-bold text-slate-900 shadow-inner">
                    </div>
                    <div class="group">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Display Limit</label>
                        <input type="number" min="4" max="12" wire:model="category_strip_limit" class="mt-2 w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-xs font-bold text-slate-900 shadow-inner">
                    </div>
                    <div class="md:col-span-2 group">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Contextual Subtitle</label>
                        <textarea wire:model="category_strip_subtitle" rows="2" class="mt-2 w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-xs font-bold text-slate-900 shadow-inner"></textarea>
                    </div>
                    <div class="group">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Visual Aesthetic</label>
                        <select wire:model="category_strip_style" class="mt-2 w-full rounded-xl border-slate-200 bg-slate-50 text-xs font-bold">
                            <option value="chips">Compact Chips</option>
                            <option value="cards">Icon Cards</option>
                            <option value="minimal">Minimal Pills</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-4">
                        <button 
                            wire:click="$set('category_show_icons', {{ !$category_show_icons ? 'true' : 'false' }})"
                            class="relative inline-flex h-7 w-14 items-center rounded-full transition-colors focus:outline-none {{ $category_show_icons ? 'bg-amber-500' : 'bg-slate-200' }}"
                        >
                            <span class="inline-block h-5 w-5 transform rounded-full bg-white transition-transform {{ $category_show_icons ? 'translate-x-8' : 'translate-x-1' }}"></span>
                        </button>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Show Icons</span>
                    </div>
                </div>
            </div>

            <!-- Catalog Hero Configuration -->
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-6">Catalog Landing Hub</p>
                <div class="grid gap-6">
                    <div class="grid gap-6 md:grid-cols-2">
                        <div class="group">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Hub Badge</label>
                            <input type="text" wire:model="catalog_hero_badge" class="mt-2 w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-xs font-bold text-slate-900 shadow-inner">
                        </div>
                        <div class="group">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Hub Headline</label>
                            <input type="text" wire:model="catalog_hero_title" class="mt-2 w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-xs font-bold text-slate-900 shadow-inner">
                        </div>
                    </div>
                    <div class="group">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Hub Narrative</label>
                        <textarea wire:model="catalog_hero_subtitle" rows="2" class="mt-2 w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-xs font-bold text-slate-900 shadow-inner"></textarea>
                    </div>
                </div>
            </div>

            <!-- Icon Mapping Matrix -->
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-6">Icon Mapping Matrix</p>
                <div class="grid gap-3 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
                    @forelse($cats as $cat)
                        <div class="group flex items-center gap-4 rounded-2xl border border-slate-100 bg-slate-50/50 p-4 transition-all hover:bg-white hover:shadow-md">
                            <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-white text-indigo-500 shadow-sm transition-colors group-hover:bg-indigo-500 group-hover:text-white">
                                <i class="fas {{ $category_icons[$cat->id] ?? 'fa-tag' }} text-sm"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Taxonomy Label</p>
                                <p class="text-xs font-black text-slate-900 truncate">{{ $cat->name }}</p>
                            </div>
                            <div class="w-48">
                                <select wire:model.live="category_icons.{{ $cat->id }}" class="w-full rounded-xl border-slate-200 bg-white text-[10px] font-bold py-2">
                                    <option value="">Default Tag</option>
                                    @foreach($iconOptions as $icon => $label)
                                        <option value="{{ $icon }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @empty
                        <div class="py-12 text-center">
                            <i class="fas fa-folder-open text-slate-200 text-3xl mb-4"></i>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">No categories identified in taxonomy</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Real-time Preview Hub -->
        <div class="space-y-6">
            <div class="rounded-[2.5rem] border border-slate-200 bg-white p-8 shadow-sm">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-8">Taxonomy Preview</p>
                
                <div class="space-y-8">
                    <!-- Catalog Hero Preview -->
                    <div class="rounded-[2rem] border border-slate-100 bg-slate-50 p-6 shadow-inner">
                        <span class="inline-flex rounded-full bg-white px-3 py-1 text-[9px] font-black uppercase tracking-widest text-slate-400 shadow-sm">{{ $catalog_hero_badge ?: 'HUB' }}</span>
                        <h4 class="mt-4 text-xl font-black text-slate-900 leading-none">{{ $catalog_hero_title ?: 'Hub Title' }}</h4>
                        <p class="mt-3 text-[10px] font-bold text-slate-400 leading-relaxed">{{ $catalog_hero_subtitle ?: 'Hub description goes here...' }}</p>
                    </div>

                    <!-- Discovery Strip Preview -->
                    <div class="space-y-4">
                        <div>
                            <p class="text-[10px] font-black text-slate-900 uppercase tracking-widest">{{ $category_strip_title ?: 'Discovery Strip' }}</p>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-tight">{{ $category_strip_subtitle ?: 'Contextual discovery message' }}</p>
                        </div>
                        
                        <div class="{{ $category_strip_style === 'cards' ? 'grid grid-cols-2 gap-3' : 'flex flex-wrap gap-2' }}">
                            @foreach($cats->take(max(1, min(6, (int) $category_strip_limit))) as $cat)
                                <div class="transition-all hover:scale-[1.02] {{ $category_strip_style === 'cards' ? 'rounded-2xl border border-slate-100 bg-white p-4 shadow-sm' : 'rounded-full border border-slate-100 bg-white px-4 py-2 shadow-sm' }}">
                                    <div class="flex items-center gap-2">
                                        @if($category_show_icons)
                                            <i class="fas {{ $category_icons[$cat->id] ?? 'fa-tag' }} text-[10px] text-amber-500"></i>
                                        @endif
                                        <span class="text-[10px] font-black text-slate-900 truncate">{{ $cat->name }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

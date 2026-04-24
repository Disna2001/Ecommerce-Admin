<x-admin.ui.panel title="Category Discovery" description="Customize the homepage category strip and the catalog landing experience from one place.">
    @php $cats = \App\Models\Category::all(); @endphp
    <div class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
        <div class="space-y-6">
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-slate-900">
                <h4 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Homepage category strip</h4>
                <div class="mt-4 grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Strip title</label>
                        <input type="text" wire:model="category_strip_title" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Visible categories</label>
                        <input type="number" min="4" max="12" wire:model="category_strip_limit" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                        @error('category_strip_limit') <span class="mt-2 block text-xs text-rose-500">{{ $message }}</span> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Strip subtitle</label>
                        <textarea wire:model="category_strip_subtitle" rows="3" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Strip style</label>
                        <select wire:model="category_strip_style" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                            <option value="chips">Compact chips</option>
                            <option value="cards">Icon cards</option>
                            <option value="minimal">Minimal pills</option>
                        </select>
                    </div>
                    <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-700 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-200">
                        <input type="checkbox" wire:model="category_show_icons" class="rounded border-slate-300 text-slate-900">
                        Show icons on category links
                    </label>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-slate-900">
                <h4 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Catalog landing hero</h4>
                <div class="mt-4 grid gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Hero badge</label>
                        <input type="text" wire:model="catalog_hero_badge" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Hero title</label>
                        <input type="text" wire:model="catalog_hero_title" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200">Hero subtitle</label>
                        <textarea wire:model="catalog_hero_subtitle" rows="3" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white"></textarea>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-slate-900">
                <h4 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Category icon mapping</h4>
                <div class="mt-4 grid gap-3">
                    @forelse($cats as $cat)
                        <div class="flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white p-4 md:flex-row md:items-center dark:border-slate-700 dark:bg-slate-950">
                            <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-violet-100 text-violet-600">
                                <i class="fas {{ $category_icons[$cat->id] ?? 'fa-tag' }}"></i>
                            </div>
                            <div class="min-w-[180px] font-medium text-slate-800 dark:text-white">{{ $cat->name }}</div>
                            <select wire:model.live="category_icons.{{ $cat->id }}" class="w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                                <option value="">-- Select Icon --</option>
                                @foreach($iconOptions as $icon => $label)
                                    <option value="{{ $icon }}">{{ $label }} ({{ $icon }})</option>
                                @endforeach
                            </select>
                        </div>
                    @empty
                        <p class="rounded-2xl border border-dashed border-slate-300 p-6 text-sm text-slate-400">No categories found yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="rounded-[1.75rem] border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-950">
            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400 dark:text-slate-500">Discovery preview</p>
            <div class="mt-4 rounded-[1.5rem] border border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-slate-900">
                <span class="inline-flex rounded-full bg-white px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-500 dark:bg-slate-950 dark:text-slate-300">{{ $catalog_hero_badge }}</span>
                <h4 class="mt-4 text-2xl font-black text-slate-900 dark:text-white">{{ $catalog_hero_title }}</h4>
                <p class="mt-3 text-sm leading-7 text-slate-500 dark:text-slate-400">{{ $catalog_hero_subtitle }}</p>
            </div>
            <div class="mt-5">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400 dark:text-slate-500">{{ $category_strip_title }}</p>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">{{ $category_strip_subtitle }}</p>
                <div class="mt-4 {{ $category_strip_style === 'cards' ? 'grid grid-cols-2 gap-3' : 'flex flex-wrap gap-3' }}">
                    @foreach($cats->take(max(1, min(6, (int) $category_strip_limit))) as $cat)
                        <div class="{{ $category_strip_style === 'cards' ? 'rounded-2xl border border-slate-200 bg-white p-4 text-sm font-semibold text-slate-700 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100' : 'rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100' }}">
                            @if($category_show_icons)
                                <i class="fas {{ $category_icons[$cat->id] ?? 'fa-tag' }} mr-2 text-violet-500"></i>
                            @endif
                            {{ $cat->name }}
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-admin.ui.panel>

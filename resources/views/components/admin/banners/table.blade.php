<x-admin.ui.panel title="Banner Queue" description="Preview what is live, what is scheduled, and which promo surface each banner controls.">
    <div class="mb-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-indigo-200 bg-indigo-50 px-4 py-4 dark:border-indigo-400/20 dark:bg-indigo-400/10">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-indigo-600 dark:text-indigo-300">Total</p>
            <p class="mt-2 text-3xl font-black text-indigo-700 dark:text-indigo-200">{{ $bannerStats['total'] }}</p>
        </div>
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-4 dark:border-emerald-400/20 dark:bg-emerald-400/10">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-600 dark:text-emerald-300">Live</p>
            <p class="mt-2 text-3xl font-black text-emerald-700 dark:text-emerald-200">{{ $bannerStats['live'] }}</p>
        </div>
        <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-4 dark:border-amber-400/20 dark:bg-amber-400/10">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-600 dark:text-amber-300">Scheduled</p>
            <p class="mt-2 text-3xl font-black text-amber-700 dark:text-amber-200">{{ $bannerStats['scheduled'] }}</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 dark:border-slate-700 dark:bg-slate-900">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Hero slots</p>
            <p class="mt-2 text-3xl font-black text-slate-900 dark:text-white">{{ $bannerStats['hero'] }}</p>
        </div>
    </div>

    <div class="mb-5 grid gap-3 lg:grid-cols-3">
        <button type="button" wire:click="applyPreset('hero_launch')" class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-left transition hover:border-slate-300 hover:bg-white dark:border-slate-800 dark:bg-slate-900 dark:hover:bg-slate-950">
            <p class="text-sm font-semibold text-slate-900 dark:text-white">Hero launch card</p>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Create a larger campaign banner for the top of the storefront.</p>
        </button>
        <button type="button" wire:click="applyPreset('promo_strip')" class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-left transition hover:border-slate-300 hover:bg-white dark:border-slate-800 dark:bg-slate-900 dark:hover:bg-slate-950">
            <p class="text-sm font-semibold text-slate-900 dark:text-white">Promo strip</p>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Quick-start a compact promotional card under the hero.</p>
        </button>
        <button type="button" wire:click="applyPreset('top_notice')" class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-left transition hover:border-slate-300 hover:bg-white dark:border-slate-800 dark:bg-slate-900 dark:hover:bg-slate-950">
            <p class="text-sm font-semibold text-slate-900 dark:text-white">Top notice</p>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Use a simpler service update or trust-message banner.</p>
        </button>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800">
            <thead class="bg-slate-50 dark:bg-slate-900/70">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Preview</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Title</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Position</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Schedule</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-900">
                @if(isset($banners) && $banners->count() > 0)
                    @foreach($banners as $banner)
                        <tr class="bg-white transition hover:bg-slate-50/80 dark:bg-slate-950/60 dark:hover:bg-slate-900/50">
                            <td class="px-4 py-4"><div class="flex h-14 w-24 items-center justify-center overflow-hidden rounded-2xl text-xs font-semibold text-white" style="background: linear-gradient(to right, {{ $banner->bg_color }}, {{ $banner->bg_color }}cc)">@if($banner->image_path)<img src="{{ Storage::url($banner->image_path) }}" class="h-14 w-24 object-cover">@else{{ \Illuminate\Support\Str::limit($banner->title, 16) }}@endif</div></td>
                            <td class="px-4 py-4"><p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $banner->title }}</p>@if($banner->subtitle)<p class="mt-1 text-xs text-slate-400">{{ \Illuminate\Support\Str::limit($banner->subtitle, 50) }}</p>@endif</td>
                            <td class="px-4 py-4"><span class="rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-700 dark:bg-indigo-400/10 dark:text-indigo-300">{{ isset($positions) && isset($positions[$banner->position]) ? $positions[$banner->position] : $banner->position }}</span></td>
                            <td class="px-4 py-4 text-xs text-slate-500 dark:text-slate-400">@if($banner->starts_at || $banner->ends_at)@if($banner->starts_at)<p>Starts {{ $banner->starts_at->format('M d, Y H:i') }}</p>@endif @if($banner->ends_at)<p class="mt-1">Ends {{ $banner->ends_at->format('M d, Y H:i') }}</p>@endif @else <span>Always live</span>@endif</td>
                            <td class="px-4 py-4"><button wire:click="toggleActive({{ $banner->id }})" class="rounded-full px-3 py-1 text-xs font-semibold {{ $banner->is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-400/10 dark:text-emerald-300' : 'bg-slate-100 text-slate-500 dark:bg-slate-900 dark:text-slate-300' }}">{{ $banner->is_active ? 'Active' : 'Inactive' }}</button>@if(!$banner->isLive() && $banner->is_active)<p class="mt-2 text-xs font-semibold text-amber-600 dark:text-amber-300">Scheduled</p>@endif</td>
                            <td class="px-4 py-4">
                                <div class="flex flex-wrap items-center gap-2">
                                    <button wire:click="moveUp({{ $banner->id }})" class="rounded-2xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-300">Up</button>
                                    <button wire:click="moveDown({{ $banner->id }})" class="rounded-2xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-300">Down</button>
                                    <button wire:click="edit({{ $banner->id }})" class="rounded-2xl bg-indigo-50 px-3 py-2 text-xs font-semibold text-indigo-600 transition hover:bg-indigo-100 dark:bg-indigo-400/10 dark:text-indigo-300">Edit</button>
                                    <button wire:click="delete({{ $banner->id }})" onclick="confirm('Delete this banner?') || event.stopImmediatePropagation()" class="rounded-2xl bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-600 transition hover:bg-rose-100 dark:bg-rose-400/10 dark:text-rose-300">Delete</button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr><td colspan="6" class="px-6 py-16"><x-admin.ui.empty-state title="No banners yet" description="Create your first banner to manage homepage promos and announcement strips from here." /></td></tr>
                @endif
            </tbody>
        </table>
    </div>
</x-admin.ui.panel>

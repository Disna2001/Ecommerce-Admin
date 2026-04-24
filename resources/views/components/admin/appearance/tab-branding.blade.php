<x-admin.ui.panel title="Branding Foundation" description="Define site name, logo, favicon, and primary storefront identity.">
    <div class="grid gap-6 lg:grid-cols-2">
        <div class="lg:col-span-2"><label class="text-sm font-semibold text-slate-700 dark:text-slate-200">Site name</label><input type="text" wire:model="site_name" class="mt-2 w-full rounded-2xl border-slate-200 bg-white text-sm shadow-none focus:border-violet-500 focus:ring-violet-500 dark:border-slate-700 dark:bg-slate-900 dark:text-white">@error('site_name') <span class="mt-2 block text-xs text-rose-500">{{ $message }}</span> @enderror</div>
        <div class="lg:col-span-2"><label class="text-sm font-semibold text-slate-700 dark:text-slate-200">Site tagline</label><input type="text" wire:model="site_tagline" class="mt-2 w-full rounded-2xl border-slate-200 bg-white text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-900">
            <label class="text-sm font-semibold text-slate-700 dark:text-slate-200">Site logo</label>
            @if($logo_path)<img src="{{ Storage::url($logo_path) }}?v={{ md5($logo_path) }}" class="mt-3 h-16 w-auto rounded-xl bg-white p-2 object-contain shadow-sm"><button type="button" wire:click="removeLogo" class="mt-3 inline-flex items-center gap-2 rounded-full border border-rose-200 bg-white px-4 py-2 text-xs font-semibold text-rose-600 transition hover:bg-rose-50"><i class="fas fa-trash-alt text-[11px]"></i>Remove logo</button>@endif
            <input type="file" wire:model="logo_image" accept="image/*" class="mt-3 block w-full text-sm text-slate-600 dark:text-slate-300">
            <p class="mt-2 text-xs text-slate-400">Recommended: transparent PNG or SVG.</p>
            <div wire:loading wire:target="logo_image" class="mt-3 rounded-2xl border border-blue-200 bg-blue-50 px-4 py-3 text-xs font-semibold text-blue-700"><i class="fas fa-spinner fa-spin mr-2"></i>Uploading logo to temporary storage...</div>
            @if($logo_image)<div class="mt-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs text-emerald-700"><div class="font-semibold">Logo ready to save</div><div class="mt-1">{{ $logo_image->getClientOriginalName() }}</div></div>@elseif($logo_path)<div class="mt-3 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-xs text-slate-500 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-400">Current logo is saved and live.</div>@endif
            @error('logo_image') <span class="mt-2 block text-xs text-rose-500">{{ $message }}</span> @enderror
        </div>
        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-900">
            <label class="text-sm font-semibold text-slate-700 dark:text-slate-200">Favicon</label>
            @if($favicon_path)<img src="{{ Storage::url($favicon_path) }}?v={{ md5($favicon_path) }}" class="mt-3 h-12 w-12 rounded-xl bg-white p-2 object-contain shadow-sm"><button type="button" wire:click="removeFavicon" class="mt-3 inline-flex items-center gap-2 rounded-full border border-rose-200 bg-white px-4 py-2 text-xs font-semibold text-rose-600 transition hover:bg-rose-50"><i class="fas fa-trash-alt text-[11px]"></i>Remove favicon</button>@endif
            <input type="file" wire:model="favicon_image" accept="image/*" class="mt-3 block w-full text-sm text-slate-600 dark:text-slate-300">
            <p class="mt-2 text-xs text-slate-400">Recommended: square PNG or ICO.</p>
            <div wire:loading wire:target="favicon_image" class="mt-3 rounded-2xl border border-blue-200 bg-blue-50 px-4 py-3 text-xs font-semibold text-blue-700"><i class="fas fa-spinner fa-spin mr-2"></i>Uploading favicon to temporary storage...</div>
            @if($favicon_image)<div class="mt-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs text-emerald-700"><div class="font-semibold">Favicon ready to save</div><div class="mt-1">{{ $favicon_image->getClientOriginalName() }}</div></div>@elseif($favicon_path)<div class="mt-3 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-xs text-slate-500 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-400">Current favicon is saved and live.</div>@endif
            @error('favicon_image') <span class="mt-2 block text-xs text-rose-500">{{ $message }}</span> @enderror
        </div>
        <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300"><input type="checkbox" wire:model="show_deals_link" class="rounded border-slate-300 text-violet-600 focus:ring-violet-500">Show Deals in navigation</label>
        <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300"><input type="checkbox" wire:model="show_new_arrivals_link" class="rounded border-slate-300 text-violet-600 focus:ring-violet-500">Show New Arrivals in navigation</label>
    </div>
</x-admin.ui.panel>

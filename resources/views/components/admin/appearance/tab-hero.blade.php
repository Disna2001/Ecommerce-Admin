<x-admin.ui.panel title="Hero Campaign Block" description="Edit the main hero message, conversion button, and visual asset shown at the top of the welcome page.">
    <div class="grid gap-6 lg:grid-cols-2">
        <div class="lg:col-span-2"><label class="block text-sm font-semibold text-slate-700 dark:text-slate-200">Hero title</label><input type="text" wire:model.live="hero_title" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white">@error('hero_title') <span class="mt-2 block text-xs text-rose-500">{{ $message }}</span> @enderror</div>
        <div><label class="block text-sm font-semibold text-slate-700 dark:text-slate-200">Hero highlight</label><input type="text" wire:model.live="hero_highlight_text" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
        <div><label class="block text-sm font-semibold text-slate-700 dark:text-slate-200">Hero microcopy</label><input type="text" wire:model.live="hero_microcopy" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
        <div class="lg:col-span-2"><label class="block text-sm font-semibold text-slate-700 dark:text-slate-200">Hero subtitle</label><input type="text" wire:model.live="hero_subtitle" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
        <div><label class="block text-sm font-semibold text-slate-700 dark:text-slate-200">Button text</label><input type="text" wire:model.live="hero_button_text" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
        <div><label class="block text-sm font-semibold text-slate-700 dark:text-slate-200">Button link</label><input type="text" wire:model="hero_button_link" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
        <div><label class="block text-sm font-semibold text-slate-700 dark:text-slate-200">Gradient from</label><div class="mt-2 flex items-center gap-3"><input type="color" wire:model.live="hero_bg_from" class="h-11 w-14 rounded-xl border border-slate-200 bg-white p-1"><input type="text" wire:model.live="hero_bg_from" class="flex-1 rounded-2xl border-slate-200 font-mono text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div></div>
        <div><label class="block text-sm font-semibold text-slate-700 dark:text-slate-200">Gradient to</label><div class="mt-2 flex items-center gap-3"><input type="color" wire:model.live="hero_bg_to" class="h-11 w-14 rounded-xl border border-slate-200 bg-white p-1"><input type="text" wire:model.live="hero_bg_to" class="flex-1 rounded-2xl border-slate-200 font-mono text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div></div>
        <div>
            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200">Layout</label>
            <select wire:model.live="hero_layout" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                <option value="split">Split showcase</option>
                <option value="centered">Centered campaign</option>
                <option value="stacked">Stacked spotlight</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200">Text alignment</label>
            <select wire:model.live="hero_alignment" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                <option value="left">Left</option>
                <option value="center">Center</option>
            </select>
        </div>
        <div class="lg:col-span-2">
            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200">Surface style</label>
            <div class="mt-2 grid gap-3 sm:grid-cols-3">
                @foreach(['soft' => 'Soft glass', 'solid' => 'Solid card', 'minimal' => 'Minimal canvas'] as $value => $label)
                    <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
                        <input type="radio" wire:model.live="hero_surface" value="{{ $value }}" class="border-slate-300 text-slate-900">
                        {{ $label }}
                    </label>
                @endforeach
            </div>
        </div>
        <div class="lg:col-span-2 rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-900">
            <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200">Hero image</label>
            @if($hero_image_path)<img src="{{ Storage::url($hero_image_path) }}?v={{ md5($hero_image_path) }}" class="mt-3 h-40 w-full rounded-2xl object-cover"><button type="button" wire:click="removeHeroImage" class="mt-3 inline-flex items-center gap-2 rounded-full border border-rose-200 bg-white px-4 py-2 text-xs font-semibold text-rose-600 transition hover:bg-rose-50"><i class="fas fa-trash-alt text-[11px]"></i>Remove hero image</button>@endif
            <input type="file" wire:model="hero_image" accept="image/*" class="mt-3 block w-full text-sm text-slate-600 dark:text-slate-300">
            <div wire:loading wire:target="hero_image" class="mt-3 rounded-2xl border border-blue-200 bg-blue-50 px-4 py-3 text-xs font-semibold text-blue-700"><i class="fas fa-spinner fa-spin mr-2"></i>Uploading hero image to temporary storage...</div>
            @if($hero_image)<div class="mt-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-3 text-xs text-emerald-700"><div class="font-semibold">Hero image ready to save</div><div class="mt-1">{{ $hero_image->getClientOriginalName() }}</div><img src="{{ $hero_image->temporaryUrl() }}" class="mt-3 h-36 w-full rounded-xl object-cover" alt="Hero preview"></div>@elseif($hero_image_path)<div class="mt-3 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-xs text-slate-500 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-400">Current hero image is saved and live.</div>@endif
            @error('hero_image') <span class="mt-2 block text-xs text-rose-500">{{ $message }}</span> @enderror
        </div>
    </div>
</x-admin.ui.panel>

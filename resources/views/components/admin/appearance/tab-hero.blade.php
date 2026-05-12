<div class="space-y-8">
    <div class="flex items-center gap-4">
        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-sky-50 text-sky-600 shadow-inner">
            <i class="fas fa-mountain-sun text-lg"></i>
        </div>
        <div>
            <h3 class="text-xl font-black text-slate-900 tracking-tight">Campaign Spotlight</h3>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">High-Impact Hero Experience</p>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <!-- Message Construction -->
        <div class="lg:col-span-2 space-y-6">
            <div class="group">
                <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 group-focus-within:text-sky-500 transition-colors">Hero Primary Title</label>
                <div class="mt-2">
                    <input type="text" wire:model.live="hero_title" class="w-full rounded-2xl border-slate-200 bg-white px-5 py-4 text-sm font-bold text-slate-900 shadow-sm transition-all focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10">
                </div>
                @error('hero_title') <p class="mt-2 text-[10px] font-black text-rose-500 uppercase tracking-widest">{{ $message }}</p> @enderror
            </div>

            <div class="grid gap-6 md:grid-cols-2">
                <div class="group">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 group-focus-within:text-sky-500 transition-colors">Catchy Highlight</label>
                    <div class="mt-2">
                        <input type="text" wire:model.live="hero_highlight_text" class="w-full rounded-2xl border-slate-200 bg-white px-5 py-3 text-xs font-bold text-slate-900 shadow-sm transition-all focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10">
                    </div>
                </div>
                <div class="group">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 group-focus-within:text-sky-500 transition-colors">Conversion Microcopy</label>
                    <div class="mt-2">
                        <input type="text" wire:model.live="hero_microcopy" class="w-full rounded-2xl border-slate-200 bg-white px-5 py-3 text-xs font-bold text-slate-900 shadow-sm transition-all focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10">
                    </div>
                </div>
            </div>

            <div class="group">
                <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 group-focus-within:text-sky-500 transition-colors">Narrative Subtitle</label>
                <div class="mt-2">
                    <textarea wire:model.live="hero_subtitle" rows="3" class="w-full rounded-2xl border-slate-200 bg-white px-5 py-4 text-xs font-bold text-slate-900 shadow-sm transition-all focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10"></textarea>
                </div>
            </div>
        </div>

        <!-- Call to Action -->
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-6">Conversion Bridge</p>
            <div class="space-y-4">
                <div class="group">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Action Text</label>
                    <input type="text" wire:model.live="hero_button_text" class="mt-2 w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-xs font-bold text-slate-900 shadow-inner">
                </div>
                <div class="group">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Action Destination</label>
                    <input type="text" wire:model="hero_button_link" class="mt-2 w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-xs font-bold text-slate-900 shadow-inner">
                </div>
            </div>
        </div>

        <!-- Atmosphere Control -->
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-6">Visual Atmosphere</p>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Gradient Start</label>
                    <div class="mt-2 flex items-center gap-2">
                        <input type="color" wire:model.live="hero_bg_from" class="h-10 w-10 overflow-hidden rounded-lg border-none p-0">
                        <input type="text" wire:model.live="hero_bg_from" class="flex-1 rounded-lg border-slate-100 bg-slate-50 px-3 py-2 font-mono text-[10px] font-black">
                    </div>
                </div>
                <div>
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Gradient End</label>
                    <div class="mt-2 flex items-center gap-2">
                        <input type="color" wire:model.live="hero_bg_to" class="h-10 w-10 overflow-hidden rounded-lg border-none p-0">
                        <input type="text" wire:model.live="hero_bg_to" class="flex-1 rounded-lg border-slate-100 bg-slate-50 px-3 py-2 font-mono text-[10px] font-black">
                    </div>
                </div>
            </div>
        </div>

        <!-- Structural Layout -->
        <div class="lg:col-span-2 grid gap-6 md:grid-cols-3">
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Layout Matrix</label>
                <select wire:model.live="hero_layout" class="mt-4 w-full rounded-xl border-slate-200 bg-slate-50 text-xs font-bold">
                    <option value="split">Split Showcase</option>
                    <option value="centered">Centered Campaign</option>
                    <option value="stacked">Stacked Spotlight</option>
                </select>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Text Alignment</label>
                <div class="mt-4 flex gap-2">
                    @foreach(['left' => 'fa-align-left', 'center' => 'fa-align-center'] as $val => $icon)
                        <button type="button" wire:click="$set('hero_alignment', '{{ $val }}')" class="flex-1 rounded-xl py-3 border {{ $hero_alignment === $val ? 'bg-slate-900 text-white border-slate-900' : 'bg-slate-50 text-slate-400 border-slate-100' }} transition-all">
                            <i class="fas {{ $icon }}"></i>
                        </button>
                    @endforeach
                </div>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Surface Finish</label>
                <select wire:model.live="hero_surface" class="mt-4 w-full rounded-xl border-slate-200 bg-slate-50 text-xs font-bold">
                    <option value="soft">Soft Glass</option>
                    <option value="solid">Solid Card</option>
                    <option value="minimal">Minimal Canvas</option>
                </select>
            </div>
        </div>

        <!-- High-Impact Image -->
        <div class="lg:col-span-2 rounded-[2.5rem] border border-slate-200 bg-white p-8 shadow-sm">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Visual Asset</p>
                    <h4 class="text-sm font-black text-slate-900">Hero Background / Foreground</h4>
                </div>
                @if($hero_image_path)
                    <button type="button" wire:click="removeHeroImage" class="rounded-xl bg-rose-50 px-4 py-2 text-[10px] font-black text-rose-600 uppercase tracking-widest hover:bg-rose-100 transition-colors">Remove Asset</button>
                @endif
            </div>

            <div class="relative flex flex-col items-center justify-center rounded-[2.5rem] border-2 border-dashed border-slate-100 bg-slate-50/50 p-12 transition-all hover:border-sky-200 group">
                @if($hero_image)
                    <div class="relative w-full max-w-2xl overflow-hidden rounded-2xl shadow-2xl transition-transform group-hover:scale-[1.02]">
                        <img src="{{ $hero_image->temporaryUrl() }}" class="h-64 w-full object-cover">
                        <div class="absolute inset-0 flex items-center justify-center bg-sky-500/20 backdrop-blur-[2px]">
                            <span class="bg-white px-4 py-2 rounded-full text-[10px] font-black uppercase tracking-widest text-sky-600 shadow-xl">New Campaign Asset Preview</span>
                        </div>
                    </div>
                @elseif($hero_image_path)
                    <div class="relative w-full max-w-2xl overflow-hidden rounded-2xl shadow-2xl transition-transform group-hover:scale-[1.02]">
                        <img src="{{ Storage::url($hero_image_path) }}?v={{ md5($hero_image_path) }}" class="h-64 w-full object-cover">
                    </div>
                @else
                    <div class="flex h-20 w-20 items-center justify-center rounded-3xl bg-white shadow-sm text-slate-200 mb-6">
                        <i class="fas fa-panorama text-3xl"></i>
                    </div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">No Hero Asset Defined</p>
                @endif

                <label class="mt-10 flex cursor-pointer items-center gap-3 rounded-2xl bg-slate-900 px-8 py-4 text-[10px] font-black text-white uppercase tracking-[0.2em] shadow-2xl transition-all hover:scale-[1.05] active:scale-[0.95]">
                    <i class="fas fa-cloud-upload-alt text-xs"></i>
                    <span>{{ $hero_image_path ? 'Replace Campaign Asset' : 'Upload Hero Asset' }}</span>
                    <input type="file" wire:model="hero_image" accept="image/*" class="hidden">
                </label>
            </div>

            <div wire:loading wire:target="hero_image" class="mt-6 flex items-center justify-center gap-3 rounded-2xl bg-sky-50 p-4 text-[10px] font-black text-sky-600 uppercase tracking-widest">
                <i class="fas fa-spinner fa-spin"></i>
                <span>Orchestrating Visual Asset...</span>
            </div>
        </div>
    </div>
</div>

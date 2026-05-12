<div class="space-y-8">
    <div class="flex items-center gap-4">
        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600 shadow-inner">
            <i class="fas fa-fingerprint text-lg"></i>
        </div>
        <div>
            <h3 class="text-xl font-black text-slate-900 tracking-tight">Core Identity</h3>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Brand DNA & Recognition</p>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <!-- Site Information -->
        <div class="lg:col-span-2 space-y-6">
            <div class="group">
                <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 group-focus-within:text-indigo-500 transition-colors">Storefront Name</label>
                <div class="mt-2 relative">
                    <input type="text" wire:model="site_name" placeholder="e.g. DISPLAY LANKA.LK" class="w-full rounded-2xl border-slate-200 bg-white px-5 py-4 text-sm font-bold text-slate-900 shadow-sm transition-all focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 placeholder:text-slate-300">
                    <div class="absolute right-5 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-indigo-400">
                        <i class="fas fa-tag"></i>
                    </div>
                </div>
                @error('site_name') <p class="mt-2 text-[10px] font-black text-rose-500 uppercase tracking-widest">{{ $message }}</p> @enderror
            </div>

            <div class="group">
                <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 group-focus-within:text-indigo-500 transition-colors">Global Tagline</label>
                <div class="mt-2 relative">
                    <input type="text" wire:model="site_tagline" placeholder="e.g. Your one-stop shop for everything" class="w-full rounded-2xl border-slate-200 bg-white px-5 py-4 text-sm font-bold text-slate-900 shadow-sm transition-all focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 placeholder:text-slate-300">
                    <div class="absolute right-5 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-indigo-400">
                        <i class="fas fa-quote-right"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Visual Assets -->
        <div class="space-y-6">
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm group">
                <div class="flex items-center justify-between mb-6">
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Primary Logo</p>
                    @if($logo_path)
                        <button type="button" wire:click="removeLogo" class="text-rose-500 hover:text-rose-700 transition-colors"><i class="fas fa-trash-alt text-xs"></i></button>
                    @endif
                </div>

                <div class="relative flex flex-col items-center justify-center rounded-[2rem] border-2 border-dashed border-slate-100 bg-slate-50/50 p-8 transition-all hover:border-indigo-200">
                    @if($logo_image)
                        <div class="relative group/logo">
                            <img src="{{ $logo_image->temporaryUrl() }}" class="h-24 w-auto object-contain transition-transform group-hover/logo:scale-105">
                            <div class="absolute -top-2 -right-2 bg-indigo-500 text-white text-[8px] font-black px-2 py-1 rounded-full uppercase tracking-widest shadow-lg">New Preview</div>
                        </div>
                    @elseif($logo_path)
                        <div class="relative group/logo">
                            <img src="{{ Storage::url($logo_path) }}?v={{ md5($logo_path) }}" class="h-24 w-auto object-contain transition-transform group-hover/logo:scale-105">
                        </div>
                    @else
                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-white shadow-sm text-slate-300 mb-4">
                            <i class="fas fa-image text-xl"></i>
                        </div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">No Logo Set</p>
                    @endif

                    <label class="mt-6 flex cursor-pointer items-center gap-2 rounded-xl bg-slate-900 px-5 py-3 text-[10px] font-black text-white uppercase tracking-widest shadow-xl transition-all hover:scale-[1.02] active:scale-[0.98]">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <span>{{ $logo_path ? 'Change Logo' : 'Upload Logo' }}</span>
                        <input type="file" wire:model="logo_image" accept="image/*" class="hidden">
                    </label>
                </div>
                
                @error('logo_image') <p class="mt-2 text-[10px] font-black text-rose-500 uppercase tracking-widest text-center">{{ $message }}</p> @enderror
                
                <div wire:loading wire:target="logo_image" class="mt-4 flex items-center justify-center gap-2 rounded-xl bg-indigo-50 p-3 text-[9px] font-black text-indigo-600 uppercase tracking-widest">
                    <i class="fas fa-circle-notch fa-spin"></i>
                    <span>Transferring Asset...</span>
                </div>
                
                <p class="mt-4 text-[9px] font-bold text-slate-400 uppercase tracking-widest text-center">Transparent PNG or SVG recommended</p>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm group">
                <div class="flex items-center justify-between mb-6">
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Favicon</p>
                    @if($favicon_path)
                        <button type="button" wire:click="removeFavicon" class="text-rose-500 hover:text-rose-700 transition-colors"><i class="fas fa-trash-alt text-xs"></i></button>
                    @endif
                </div>

                <div class="relative flex flex-col items-center justify-center rounded-[2rem] border-2 border-dashed border-slate-100 bg-slate-50/50 p-8 transition-all hover:border-indigo-200">
                    @if($favicon_image)
                        <div class="relative group/favicon">
                            <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-white shadow-md p-3 relative">
                                <img src="{{ $favicon_image->temporaryUrl() }}" class="h-full w-full object-contain">
                                <div class="absolute -top-2 -right-2 bg-indigo-500 text-white text-[8px] font-black px-2 py-1 rounded-full uppercase tracking-widest shadow-lg">New</div>
                            </div>
                        </div>
                    @elseif($favicon_path)
                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-white shadow-md p-3">
                            <img src="{{ Storage::url($favicon_path) }}?v={{ md5($favicon_path) }}" class="h-full w-full object-contain">
                        </div>
                    @else
                        <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-white shadow-sm text-slate-300 mb-4">
                            <i class="fas fa-gem text-xl"></i>
                        </div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">No Favicon Set</p>
                    @endif

                    <label class="mt-6 flex cursor-pointer items-center gap-2 rounded-xl bg-slate-900 px-5 py-3 text-[10px] font-black text-white uppercase tracking-widest shadow-xl transition-all hover:scale-[1.02] active:scale-[0.98]">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <span>{{ $favicon_path ? 'Change Favicon' : 'Upload Favicon' }}</span>
                        <input type="file" wire:model="favicon_image" accept="image/*" class="hidden">
                    </label>
                </div>

                @error('favicon_image') <p class="mt-2 text-[10px] font-black text-rose-500 uppercase tracking-widest text-center">{{ $message }}</p> @enderror

                <div wire:loading wire:target="favicon_image" class="mt-4 flex items-center justify-center gap-2 rounded-xl bg-indigo-50 p-3 text-[9px] font-black text-indigo-600 uppercase tracking-widest">
                    <i class="fas fa-circle-notch fa-spin"></i>
                    <span>Transferring Asset...</span>
                </div>

                <p class="mt-4 text-[9px] font-bold text-slate-400 uppercase tracking-widest text-center">Square 32x32 or 64x64px recommended</p>
            </div>
        </div>
    </div>
</div>

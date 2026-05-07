<div 
    x-data="{ show: @entangle('isOpen') }" 
    x-show="show" 
    class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md overflow-y-auto custom-scrollbar"
    x-cloak
>
    <div 
        x-show="show" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95 translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-4"
        class="relative w-full max-w-4xl rounded-[3rem] border border-slate-200 bg-white shadow-2xl overflow-hidden"
    >
        <!-- Modal Header -->
        <div class="border-b border-slate-100 bg-slate-50/50 p-8">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Campaign Deployment</p>
                    <h3 class="mt-2 text-2xl font-black tracking-tight text-slate-900">{{ $banner_id ? 'Refine Campaign' : 'Deploy New Campaign' }}</h3>
                </div>
                <button @click="show = false" class="flex h-10 w-10 items-center justify-center rounded-2xl bg-white text-slate-400 shadow-sm transition-all hover:bg-slate-900 hover:text-white">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
        </div>

        <!-- Modal Body -->
        <div class="p-8 max-h-[70vh] overflow-y-auto custom-scrollbar">
            <div class="grid gap-8 lg:grid-cols-2">
                <!-- Visual Asset Section -->
                <div class="space-y-6">
                    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm group">
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-6">Visual Asset</p>
                        
                        <div class="relative flex flex-col items-center justify-center rounded-[2rem] border-2 border-dashed border-slate-100 bg-slate-50/50 p-8 transition-all hover:border-indigo-200 group/upload">
                            @if($image)
                                <img src="{{ $image->temporaryUrl() }}" class="h-40 w-full object-cover rounded-2xl shadow-xl">
                            @elseif($image_path)
                                <img src="{{ Storage::url($image_path) }}" class="h-40 w-full object-cover rounded-2xl shadow-xl">
                            @else
                                <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-white shadow-sm text-slate-200 mb-4">
                                    <i class="fas fa-panorama text-2xl"></i>
                                </div>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">No Asset Loaded</p>
                            @endif

                            <label class="mt-6 flex cursor-pointer items-center gap-2 rounded-xl bg-slate-900 px-5 py-3 text-[10px] font-black text-white uppercase tracking-widest shadow-xl transition-all hover:scale-[1.02] active:scale-[0.98]">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <span>{{ $image || $image_path ? 'Change Asset' : 'Upload Asset' }}</span>
                                <input type="file" wire:model="image" accept="image/*" class="hidden">
                            </label>
                        </div>
                        
                        <div wire:loading wire:target="image" class="mt-4 flex items-center justify-center gap-2 rounded-xl bg-indigo-50 p-3 text-[9px] font-black text-indigo-600 uppercase tracking-widest">
                            <i class="fas fa-circle-notch fa-spin"></i>
                            <span>Transferring Asset...</span>
                        </div>
                    </div>

                    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-6">Chromatic Signature</p>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Background</label>
                                <div class="mt-2 flex items-center gap-2">
                                    <input type="color" wire:model.live="bg_color" class="h-10 w-10 overflow-hidden rounded-lg border-none p-0">
                                    <input type="text" wire:model.live="bg_color" class="flex-1 rounded-lg border-slate-100 bg-slate-50 px-3 py-2 font-mono text-[10px] font-black uppercase">
                                </div>
                            </div>
                            <div>
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Typography</label>
                                <div class="mt-2 flex items-center gap-2">
                                    <input type="color" wire:model.live="text_color" class="h-10 w-10 overflow-hidden rounded-lg border-none p-0">
                                    <input type="text" wire:model.live="text_color" class="flex-1 rounded-lg border-slate-100 bg-slate-50 px-3 py-2 font-mono text-[10px] font-black uppercase">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Campaign Details Section -->
                <div class="space-y-6">
                    <div class="grid gap-6 md:grid-cols-2">
                        <div class="group/input">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Campaign Title</label>
                            <input type="text" wire:model="title" class="mt-2 w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-xs font-bold text-slate-900 shadow-inner">
                            @error('title') <p class="mt-1 text-[9px] font-black text-rose-500 uppercase">{{ $message }}</p> @enderror
                        </div>
                        <div class="group/input">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Sub-headline</label>
                            <input type="text" wire:model="subtitle" class="mt-2 w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-xs font-bold text-slate-900 shadow-inner">
                        </div>
                    </div>

                    <div class="group/input">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Campaign Narrative (Caption)</label>
                        <textarea wire:model="caption" rows="3" class="mt-2 w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-xs font-bold text-slate-900 shadow-inner"></textarea>
                    </div>

                    <div class="grid gap-6 md:grid-cols-2">
                        <div class="group/input">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Button Label</label>
                            <input type="text" wire:model="button_text" class="mt-2 w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-xs font-bold text-slate-900 shadow-inner">
                        </div>
                        <div class="group/input">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Target Link</label>
                            <input type="text" wire:model="button_link" class="mt-2 w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-xs font-bold text-slate-900 shadow-inner">
                        </div>
                    </div>

                    <div class="grid gap-6 md:grid-cols-2">
                        <div class="group/input">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Strategic Position</label>
                            <select wire:model="position" class="mt-2 w-full rounded-xl border-slate-200 bg-slate-50 text-xs font-bold py-3 shadow-inner">
                                @foreach($positions as $val => $lbl)
                                    <option value="{{ $val }}">{{ $lbl }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="group/input">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Scheduling Order</label>
                            <input type="number" wire:model="sort_order" class="mt-2 w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-xs font-bold text-slate-900 shadow-inner">
                        </div>
                    </div>

                    <div class="grid gap-6 md:grid-cols-2">
                        <div class="group/input">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Activation Start</label>
                            <input type="datetime-local" wire:model="starts_at" class="mt-2 w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-xs font-bold text-slate-900 shadow-inner">
                        </div>
                        <div class="group/input">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Activation End</label>
                            <input type="datetime-local" wire:model="ends_at" class="mt-2 w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-3 text-xs font-bold text-slate-900 shadow-inner">
                        </div>
                    </div>

                    <div class="flex items-center gap-4 p-4 rounded-[2rem] bg-slate-900 text-white shadow-xl">
                        <button 
                            wire:click="$set('is_active', {{ !$is_active ? 'true' : 'false' }})"
                            type="button"
                            class="relative inline-flex h-8 w-16 items-center rounded-full transition-colors {{ $is_active ? 'bg-indigo-500' : 'bg-white/10' }}"
                        >
                            <span class="inline-block h-6 w-6 transform rounded-full bg-white transition-transform {{ $is_active ? 'translate-x-9' : 'translate-x-1' }}"></span>
                        </button>
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-widest leading-none">Campaign Pulse</p>
                            <p class="text-[9px] font-bold text-white/40 uppercase tracking-tighter mt-1">{{ $is_active ? 'Active & Verifiable' : 'Dormant (Draft)' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="border-t border-slate-100 bg-slate-50/50 p-8 flex items-center justify-end gap-3">
            <button @click="show = false" class="rounded-2xl px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-slate-900 transition-colors">Discard Draft</button>
            <button wire:click="store" class="flex items-center gap-3 rounded-2xl bg-slate-900 px-10 py-4 text-[10px] font-black text-white uppercase tracking-[0.2em] shadow-2xl shadow-slate-200 transition-all hover:scale-[1.02] active:scale-[0.98]">
                <i class="fas fa-rocket text-[10px] opacity-50"></i>
                Deploy Campaign
            </button>
        </div>
    </div>
</div>

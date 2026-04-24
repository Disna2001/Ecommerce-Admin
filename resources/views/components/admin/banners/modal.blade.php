@if($isOpen)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-screen items-center justify-center px-4 py-8">
            <div class="fixed inset-0 bg-slate-950/60"></div>
            <div class="relative z-10 w-full max-w-3xl overflow-hidden rounded-[2rem] border border-white/70 bg-white shadow-2xl dark:border-white/10 dark:bg-slate-950">
                <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4 dark:border-slate-800"><div><p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-400">Banner Builder</p><h3 class="mt-1 text-xl font-bold text-slate-900 dark:text-white">{{ $banner_id ? 'Edit campaign banner' : 'New campaign banner' }}</h3></div><button wire:click="$set('isOpen', false)" class="text-slate-400 transition hover:text-slate-700 dark:hover:text-slate-200"><i class="fas fa-times text-lg"></i></button></div>
                <form wire:submit.prevent="store">
                    <div class="grid max-h-[72vh] grid-cols-1 gap-5 overflow-y-auto p-6 md:grid-cols-2">
                        <div class="md:col-span-2 grid gap-3 md:grid-cols-3">
                            <button type="button" wire:click="applyPreset('hero_launch')" class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-left text-sm font-semibold text-slate-700 transition hover:bg-white dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">Hero launch</button>
                            <button type="button" wire:click="applyPreset('promo_strip')" class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-left text-sm font-semibold text-slate-700 transition hover:bg-white dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">Promo strip</button>
                            <button type="button" wire:click="applyPreset('top_notice')" class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-left text-sm font-semibold text-slate-700 transition hover:bg-white dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">Top notice</button>
                        </div>
                        <div class="md:col-span-2"><label class="text-sm font-semibold text-slate-700 dark:text-slate-200">Title</label><input type="text" wire:model="title" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white">@error('title') <span class="mt-2 block text-xs text-rose-500">{{ $message }}</span> @enderror</div>
                        <div><label class="text-sm font-semibold text-slate-700 dark:text-slate-200">Subtitle</label><input type="text" wire:model="subtitle" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
                        <div><label class="text-sm font-semibold text-slate-700 dark:text-slate-200">Position</label><select wire:model="position" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white">@if(isset($positions) && is_array($positions))@foreach($positions as $val => $label)<option value="{{ $val }}">{{ $label }}</option>@endforeach @endif</select></div>
                        <div class="md:col-span-2"><label class="text-sm font-semibold text-slate-700 dark:text-slate-200">Caption</label><textarea wire:model="caption" rows="3" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></textarea></div>
                        <div><label class="text-sm font-semibold text-slate-700 dark:text-slate-200">Button text</label><input type="text" wire:model="button_text" placeholder="Shop now" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
                        <div><label class="text-sm font-semibold text-slate-700 dark:text-slate-200">Button link</label><input type="text" wire:model="button_link" placeholder="/products" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
                        <div><label class="text-sm font-semibold text-slate-700 dark:text-slate-200">Background color</label><div class="mt-2 flex gap-2"><input type="color" wire:model="bg_color" class="h-11 w-14 rounded-xl border border-slate-200 bg-white p-1"><input type="text" wire:model="bg_color" class="flex-1 rounded-2xl border-slate-200 font-mono text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div></div>
                        <div><label class="text-sm font-semibold text-slate-700 dark:text-slate-200">Text color</label><div class="mt-2 flex gap-2"><input type="color" wire:model="text_color" class="h-11 w-14 rounded-xl border border-slate-200 bg-white p-1"><input type="text" wire:model="text_color" class="flex-1 rounded-2xl border-slate-200 font-mono text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div></div>
                        <div><label class="text-sm font-semibold text-slate-700 dark:text-slate-200">Starts at</label><input type="datetime-local" wire:model="starts_at" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
                        <div><label class="text-sm font-semibold text-slate-700 dark:text-slate-200">Ends at</label><input type="datetime-local" wire:model="ends_at" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white">@error('ends_at') <span class="mt-2 block text-xs text-rose-500">{{ $message }}</span> @enderror</div>
                        <div class="md:col-span-2 rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-900">
                            <label class="text-sm font-semibold text-slate-700 dark:text-slate-200">Banner image</label>
                            @if($image_path)<img src="{{ Storage::url($image_path) }}" class="mt-2 h-28 w-full rounded-2xl object-cover">@endif
                            <input type="file" wire:model="image" accept="image/*" class="mt-3 block w-full text-sm text-slate-600 dark:text-slate-300">
                            <div wire:loading wire:target="image" class="mt-3 rounded-2xl border border-blue-200 bg-blue-50 px-4 py-3 text-xs font-semibold text-blue-700"><i class="fas fa-spinner fa-spin mr-2"></i>Uploading banner image...</div>
                            @if($image)
                                <div class="mt-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-3 text-xs text-emerald-700">
                                    <div class="font-semibold">Image ready to save</div>
                                    <div class="mt-1">{{ $image->getClientOriginalName() }}</div>
                                    <img src="{{ $image->temporaryUrl() }}" class="mt-3 h-36 w-full rounded-xl object-cover" alt="Banner preview">
                                </div>
                            @endif
                        </div>
                        <div class="md:col-span-2 rounded-[1.75rem] border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-950">
                            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400 dark:text-slate-500">Live preview</p>
                            <div class="mt-4 overflow-hidden rounded-[1.5rem] border border-slate-200 dark:border-slate-800">
                                <div class="p-5 text-white" style="background:linear-gradient(135deg, {{ $bg_color ?: '#4f46e5' }}, {{ $bg_color ?: '#4f46e5' }}cc); color: {{ $text_color ?: '#ffffff' }};">
                                    @if($subtitle)<p class="text-xs font-semibold uppercase tracking-[0.2em]" style="color: {{ $text_color ?: '#ffffff' }}cc;">{{ $subtitle }}</p>@endif
                                    <h4 class="mt-3 text-2xl font-black">{{ $title ?: 'Banner title preview' }}</h4>
                                    @if($caption)<p class="mt-2 text-sm leading-7" style="color: {{ $text_color ?: '#ffffff' }}dd;">{{ $caption }}</p>@endif
                                    @if($button_text)<div class="mt-4 inline-flex rounded-full bg-white px-4 py-2 text-sm font-semibold text-slate-900">{{ $button_text }}</div>@endif
                                </div>
                                @if($image)
                                    <img src="{{ $image->temporaryUrl() }}" class="h-40 w-full object-cover" alt="Banner preview image">
                                @elseif($image_path)
                                    <img src="{{ Storage::url($image_path) }}" class="h-40 w-full object-cover" alt="Saved banner image">
                                @endif
                            </div>
                        </div>
                        <label class="flex items-center gap-3 pt-8 text-sm font-semibold text-slate-700 dark:text-slate-200"><input type="checkbox" wire:model="is_active" class="rounded border-slate-300 text-indigo-500">Active</label>
                        <div><label class="text-sm font-semibold text-slate-700 dark:text-slate-200">Sort order</label><input type="number" wire:model="sort_order" min="0" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none dark:border-slate-700 dark:bg-slate-900 dark:text-white"></div>
                    </div>
                    <div class="flex justify-end gap-3 border-t border-slate-200 px-6 py-4 dark:border-slate-800"><button type="button" wire:click="$set('isOpen', false)" class="rounded-2xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-900">Cancel</button><button type="submit" class="rounded-2xl bg-indigo-600 px-5 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700">{{ $banner_id ? 'Update banner' : 'Create banner' }}</button></div>
                </form>
            </div>
        </div>
    </div>
@endif

<x-admin.ui.panel title="Storefront Palette" description="Control the welcome-page and storefront accent colors used across the public UI.">
    <div class="grid gap-4 lg:grid-cols-2">
        @foreach([['primary_color', 'Primary'], ['secondary_color', 'Secondary'], ['accent_color', 'Accent'], ['text_color', 'Text'], ['bg_color', 'Background'], ['nav_bg_color', 'Navigation']] as [$field, $label])
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-900"><label class="block text-sm font-semibold text-slate-700 dark:text-slate-200">{{ $label }} color</label><div class="mt-3 flex items-center gap-3"><input type="color" wire:model.live="{{ $field }}" class="h-11 w-14 rounded-xl border border-slate-200 bg-white p-1"><input type="text" wire:model="{{ $field }}" class="flex-1 rounded-2xl border-slate-200 font-mono text-sm shadow-none dark:border-slate-700 dark:bg-slate-950 dark:text-white"><div class="h-10 w-10 rounded-xl border border-slate-200" style="background: {{ $this->$field }}"></div></div></div>
        @endforeach
    </div>
</x-admin.ui.panel>

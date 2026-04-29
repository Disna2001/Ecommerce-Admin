<aside class="admin-surface min-w-0 rounded-2xl border border-slate-200/80 bg-white/74 p-3 shadow-sm dark:border-slate-800 dark:bg-slate-950/58 lg:sticky lg:top-24">
    <p class="px-2 text-xs font-semibold uppercase tracking-[0.24em] text-slate-400 dark:text-slate-500">Sections</p>
    <div class="mt-3 space-y-1.5">
        @foreach(['communications' => ['Email & Alerts', 'fa-envelope'], 'hosting' => ['Hosting & Identity', 'fa-server'], 'billing' => ['Billing & Print', 'fa-print'], 'api_keys' => ['API Keys', 'fa-key'], 'whatsapp' => ['WhatsApp', 'fa-comment-dots'], 'ai' => ['AI Operations', 'fa-robot'], 'access' => ['Access Guide', 'fa-user-shield']] as $tab => [$label, $icon])
            <button wire:click="$set('activeTab', '{{ $tab }}')" @class(['flex w-full items-center gap-3 rounded-xl border px-3 py-2.5 text-left text-sm font-medium transition', 'border-slate-900 bg-slate-900 text-white dark:border-white dark:bg-white dark:text-slate-900' => $activeTab === $tab, 'border-transparent text-slate-600 hover:border-slate-200 hover:bg-slate-50 hover:text-slate-900 dark:text-slate-300 dark:hover:border-slate-800 dark:hover:bg-slate-900 dark:hover:text-white' => $activeTab !== $tab])>
                <i class="fas {{ $icon }} w-4 text-center"></i><span>{{ $label }}</span>
            </button>
        @endforeach
    </div>
</aside>

<aside class="admin-surface rounded-[2rem] border border-white/60 bg-white/90 p-4 shadow-[0_18px_60px_rgba(15,23,42,0.08)] dark:border-white/10 dark:bg-slate-950/75">
    <p class="px-2 text-xs font-semibold uppercase tracking-[0.24em] text-slate-400 dark:text-slate-500">Sections</p>
    <div class="mt-3 space-y-2">
        @foreach(['communications' => ['Email & Alerts', 'fa-envelope'], 'hosting' => ['Hosting & Identity', 'fa-server'], 'api_keys' => ['API Keys', 'fa-key'], 'whatsapp' => ['WhatsApp', 'fa-comment-dots'], 'ai' => ['AI Operations', 'fa-robot'], 'access' => ['Access Guide', 'fa-user-shield']] as $tab => [$label, $icon])
            <button wire:click="$set('activeTab', '{{ $tab }}')" @class(['flex w-full items-center gap-3 rounded-2xl px-4 py-3 text-left text-sm font-medium transition', 'bg-slate-900 text-white shadow-md shadow-slate-900/10 dark:bg-white dark:text-slate-900' => $activeTab === $tab, 'text-slate-600 hover:bg-slate-50 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-900 dark:hover:text-white' => $activeTab !== $tab])>
                <i class="fas {{ $icon }} w-4 text-center"></i><span>{{ $label }}</span>
            </button>
        @endforeach
    </div>
</aside>

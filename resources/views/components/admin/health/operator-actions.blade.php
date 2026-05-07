<div class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-sm">
    <div class="flex items-center gap-4 mb-8">
        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-900 text-white shadow-lg shadow-slate-200"><i class="fas fa-screwdriver-wrench text-sm"></i></div>
        <div>
            <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest">Maintenance Desk</h3>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Global State Purge & Optimization</p>
        </div>
    </div>

    <div class="space-y-3">
        @foreach([
            ['Optimize Storage', 'php artisan storage:link', 'fa-hard-drive'],
            ['Flush Cache', 'php artisan cache:clear', 'fa-bolt-lightning'],
            ['Optimize Config', 'php artisan config:cache', 'fa-gears'],
            ['Restart Queues', 'php artisan queue:restart', 'fa-arrows-rotate']
        ] as [$label, $command, $icon])
            <button type="button" x-data @click="if(confirm('Execute {{ $command }}?')) $wire.runCommand('{{ $command }}')" class="group w-full flex items-center justify-between rounded-2xl border border-slate-100 bg-slate-50 p-4 transition-all hover:bg-white hover:border-slate-900 hover:shadow-lg">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white text-slate-400 group-hover:bg-slate-900 group-hover:text-white transition-colors">
                        <i class="fas {{ $icon }} text-xs"></i>
                    </div>
                    <div>
                        <p class="text-sm font-black text-slate-900">{{ $label }}</p>
                        <p class="text-[9px] font-mono text-slate-400 uppercase tracking-tighter">{{ $command }}</p>
                    </div>
                </div>
                <i class="fas fa-terminal text-[10px] opacity-10 group-hover:opacity-40 transition-opacity"></i>
            </button>
        @endforeach
    </div>
</div>

@props(['maintenanceMode' => false, 'backupFile' => null])

<div class="space-y-12">
    <!-- Global System State -->
    <div class="rounded-[2.5rem] border border-slate-900 bg-slate-900 p-8 text-white shadow-2xl relative overflow-hidden">
        <div class="absolute right-0 top-0 -mr-16 -mt-16 h-64 w-64 rounded-full bg-white/5"></div>
        <div class="relative z-10 flex flex-col gap-8 md:flex-row md:items-center md:justify-between">
            <div class="flex items-center gap-6">
                <div class="flex h-16 w-16 items-center justify-center rounded-3xl bg-{{ $maintenanceMode ? 'rose' : 'emerald' }}-500 shadow-xl shadow-{{ $maintenanceMode ? 'rose' : 'emerald' }}-500/20">
                    <i class="fas {{ $maintenanceMode ? 'fa-stop-circle animate-pulse' : 'fa-play-circle' }} text-2xl text-white"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black tracking-tight">{{ $maintenanceMode ? 'System Suspended' : 'System Operational' }}</h2>
                    <p class="text-[10px] font-bold text-white/40 uppercase tracking-[0.2em]">{{ $maintenanceMode ? 'Maintenance protocols are currently active' : 'All public services are nominal and active' }}</p>
                </div>
            </div>

            <div class="flex items-center gap-6 rounded-3xl bg-white/5 p-4 border border-white/10">
                <div class="text-right hidden sm:block">
                    <p class="text-[10px] font-black uppercase tracking-widest text-white/60">Maintenance Mode</p>
                    <p class="text-xs font-bold text-{{ $maintenanceMode ? 'rose' : 'emerald' }}-400">{{ $maintenanceMode ? 'ACTIVE' : 'INACTIVE' }}</p>
                </div>
                <button 
                    wire:click="toggleMaintenanceMode"
                    class="relative inline-flex h-8 w-16 items-center rounded-full transition-colors focus:outline-none {{ $maintenanceMode ? 'bg-rose-500' : 'bg-slate-700' }}"
                >
                    <span 
                        class="inline-block h-6 w-6 transform rounded-full bg-white transition-transform {{ $maintenanceMode ? 'translate-x-9' : 'translate-x-1' }}"
                    ></span>
                </button>
            </div>
        </div>

        @if($maintenanceMode)
            <div class="mt-8 rounded-2xl bg-white/5 border border-white/10 p-4">
                <div class="flex items-center gap-3">
                    <i class="fas fa-key text-indigo-400 text-xs"></i>
                    <p class="text-[10px] font-bold text-white/60 uppercase tracking-widest">Bypass Secret: <span class="text-white font-black select-all">admin-bypass</span></p>
                </div>
            </div>
        @endif
    </div>

    <div class="grid gap-8 lg:grid-cols-2">
        <!-- Backup Section -->
        <div class="space-y-6">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600 shadow-inner">
                    <i class="fas fa-cloud-arrow-down text-lg"></i>
                </div>
                <div>
                    <h3 class="text-xl font-black text-slate-900 tracking-tight">System State Archive</h3>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Generate Full Data Export</p>
                </div>
            </div>

            <div class="rounded-[2.5rem] border border-slate-200 bg-white p-8 shadow-sm">
                <p class="text-sm font-medium leading-relaxed text-slate-500 mb-8">
                    Generate a high-fidelity snapshot of your entire commerce ecosystem. This includes all products, customers, orders, and system configurations packed into a portable ZIP archive.
                </p>

                <div class="space-y-4">
                    <div class="flex items-center gap-4 rounded-2xl bg-slate-50 p-4 border border-slate-100">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white text-emerald-500 shadow-sm">
                            <i class="fas fa-database text-xs"></i>
                        </div>
                        <div>
                            <p class="text-xs font-black text-slate-900">Database Schema & Data</p>
                            <p class="text-[10px] font-medium text-slate-400 uppercase tracking-widest">Included in archive</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 rounded-2xl bg-slate-50 p-4 border border-slate-100 opacity-50">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white text-slate-400 shadow-sm">
                            <i class="fas fa-images text-xs"></i>
                        </div>
                        <div>
                            <p class="text-xs font-black text-slate-900">Media Assets (Storage)</p>
                            <p class="text-[10px] font-medium text-slate-400 uppercase tracking-widest">Excluded (Backup via FTP/S3)</p>
                        </div>
                    </div>
                </div>

                <div class="mt-10">
                    <button wire:click="downloadBackup" wire:loading.attr="disabled" class="group relative flex w-full items-center justify-center gap-3 rounded-2xl bg-slate-900 px-8 py-5 text-[10px] font-black text-white uppercase tracking-[0.2em] shadow-2xl shadow-slate-200 transition-all hover:scale-[1.02] active:scale-[0.98] disabled:opacity-50">
                        <i class="fas fa-download text-[10px] opacity-50 group-hover:opacity-100 transition-opacity"></i>
                        <span>Generate & Download Backup</span>
                    </button>
                    <p class="mt-4 text-center text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                        Process may take several seconds depending on data volume.
                    </p>
                </div>
            </div>
        </div>

        <!-- Restore Section -->
        <div class="space-y-6">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-50 text-rose-600 shadow-inner">
                    <i class="fas fa-cloud-arrow-up text-lg"></i>
                </div>
                <div>
                    <h3 class="text-xl font-black text-slate-900 tracking-tight">System Restoration</h3>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Restore System from Archive</p>
                </div>
            </div>

            <div class="rounded-[2.5rem] border border-slate-200 bg-white p-8 shadow-sm relative overflow-hidden">
                <div class="absolute inset-0 bg-rose-50/10 pointer-events-none"></div>
                
                <div class="relative z-10">
                    <div class="mb-8 rounded-2xl bg-rose-50 border border-rose-100 p-4">
                        <div class="flex gap-3">
                            <i class="fas fa-triangle-exclamation text-rose-500 mt-1"></i>
                            <div>
                                <p class="text-xs font-black text-rose-700 uppercase tracking-widest">Critical Operation</p>
                                <p class="mt-1 text-[11px] font-medium text-rose-600 leading-relaxed">
                                    Restoring data will <span class="font-black underline">TRUNCATE</span> all current tables and overwrite them with archive data. This action is irreversible.
                                </p>
                            </div>
                        </div>
                    </div>

                    <form wire:submit.prevent="restoreBackup" class="space-y-8">
                        <div 
                            x-data="{ isUploading: false, progress: 0 }"
                            x-on:livewire-upload-start="isUploading = true"
                            x-on:livewire-upload-finish="isUploading = false"
                            x-on:livewire-upload-error="isUploading = false"
                            x-on:livewire-upload-progress="progress = $event.detail.progress"
                            class="relative"
                        >
                            <label class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-slate-200 rounded-[2rem] bg-slate-50/50 cursor-pointer hover:bg-slate-50 hover:border-indigo-400 transition-all group">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white shadow-sm mb-4 text-slate-400 group-hover:text-indigo-500 transition-colors">
                                        <i class="fas fa-file-zipper text-lg"></i>
                                    </div>
                                    <p class="mb-2 text-xs font-black text-slate-900 uppercase tracking-widest">Select Backup ZIP</p>
                                    <p class="text-[10px] font-medium text-slate-400">Max file size: 50MB</p>
                                </div>
                                <input wire:model="backupFile" type="file" class="hidden" accept=".zip" />
                            </label>

                            <div x-show="isUploading" class="mt-4">
                                <div class="h-1 w-full bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-indigo-500 transition-all duration-300" :style="'width: ' + progress + '%'"></div>
                                </div>
                                <p class="mt-2 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center" x-text="'Uploading Archive: ' + progress + '%'"></p>
                            </div>

                            @if ($backupFile)
                                <div class="mt-4 flex items-center justify-between rounded-xl bg-emerald-50 border border-emerald-100 p-3">
                                    <div class="flex items-center gap-3">
                                        <i class="fas fa-file-circle-check text-emerald-500 text-xs"></i>
                                        <span class="text-[10px] font-black text-emerald-700 uppercase">{{ $backupFile->getClientOriginalName() }}</span>
                                    </div>
                                    <button type="button" wire:click="$set('backupFile', null)" class="text-rose-500 hover:text-rose-700"><i class="fas fa-times text-xs"></i></button>
                                </div>
                            @endif
                            @error('backupFile') <p class="mt-2 text-[10px] font-black text-rose-500 uppercase">{{ $message }}</p> @enderror
                        </div>

                        <button 
                            type="submit" 
                            onclick="return confirm('CRITICAL: Are you absolutely sure you want to restore this backup? ALL CURRENT DATA WILL BE LOST.')"
                            class="group relative flex w-full items-center justify-center gap-3 rounded-2xl bg-rose-600 px-8 py-5 text-[10px] font-black text-white uppercase tracking-[0.2em] shadow-2xl shadow-rose-100 transition-all hover:bg-rose-700 hover:scale-[1.02] active:scale-[0.98] disabled:opacity-50"
                            {{ !$backupFile ? 'disabled' : '' }}
                        >
                            <i class="fas fa-triangle-exclamation text-[10px] opacity-50 group-hover:opacity-100 transition-opacity"></i>
                            <span>Execute System Restoration</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Maintenance Commands -->
    <div class="space-y-6">
        <div class="flex items-center gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-600 shadow-inner">
                <i class="fas fa-wrench text-lg"></i>
            </div>
            <div>
                <h3 class="text-xl font-black text-slate-900 tracking-tight">Technical Operations</h3>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Maintenance Utility Suite</p>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            @foreach([
                ['label' => 'Cache Cleanup', 'desc' => 'Flush system & config cache', 'icon' => 'fa-broom', 'action' => 'cache:clear'],
                ['label' => 'Route Optimization', 'desc' => 'Rebuild route cache matrix', 'icon' => 'fa-route', 'action' => 'route:cache'],
                ['label' => 'View Compiler', 'desc' => 'Prune and recompile blade views', 'icon' => 'fa-paint-brush', 'action' => 'view:clear'],
            ] as $util)
                <div class="group rounded-3xl border border-slate-200 bg-white p-6 transition-all hover:border-slate-900 hover:shadow-xl">
                    <div class="flex items-center gap-4">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-50 text-slate-400 group-hover:bg-slate-900 group-hover:text-white transition-colors">
                            <i class="fas {{ $util['icon'] }} text-xs"></i>
                        </div>
                        <div>
                            <p class="text-[11px] font-black text-slate-900 uppercase tracking-tighter">{{ $util['label'] }}</p>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">{{ $util['desc'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

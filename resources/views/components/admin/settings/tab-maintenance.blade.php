@props(['maintenanceMode' => false, 'backupFile' => null])

<div class="space-y-6">
    <!-- Distinct System Status & Maintenance Toggle Accent Card -->
    <div class="rounded-xl border {{ $maintenanceMode ? 'border-amber-300 bg-amber-50' : 'border-slate-800 bg-slate-900' }} p-6 text-{{ $maintenanceMode ? 'slate-900' : 'white' }} shadow-xs">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg {{ $maintenanceMode ? 'bg-amber-500 text-white' : 'bg-emerald-500 text-white' }} shadow-xs">
                    <i class="fas {{ $maintenanceMode ? 'fa-triangle-exclamation' : 'fa-check-double' }} text-lg"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold">{{ $maintenanceMode ? 'Maintenance Mode Active' : 'System Live & Operational' }}</h2>
                    <p class="text-xs {{ $maintenanceMode ? 'text-amber-800' : 'text-slate-400' }} font-medium mt-0.5">{{ $maintenanceMode ? 'Public storefront access is suspended' : 'All public storefront services are nominal' }}</p>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <button 
                    wire:click="toggleMaintenanceMode"
                    class="inline-flex items-center gap-2 rounded-lg {{ $maintenanceMode ? 'bg-emerald-600 text-white hover:bg-emerald-700' : 'bg-rose-600 text-white hover:bg-rose-700' }} px-4 py-2 text-xs font-semibold transition-colors shadow-xs"
                >
                    <i class="fas {{ $maintenanceMode ? 'fa-play' : 'fa-power-off' }} text-xs"></i>
                    <span>{{ $maintenanceMode ? 'Bring System Live' : 'Enable Maintenance' }}</span>
                </button>
            </div>
        </div>

        @if($maintenanceMode)
            <div class="mt-4 rounded-lg bg-white/60 border border-amber-200 p-3 text-xs flex items-center justify-between" x-data="{ show: false }">
                <span class="font-bold text-amber-900">Bypass Secret:</span>
                <div class="flex items-center gap-2">
                    <span class="font-mono font-bold" x-text="show ? 'admin-bypass' : '••••••••••••'"></span>
                    <button type="button" @click="show = !show" class="text-amber-700 hover:text-amber-900"><i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i></button>
                </div>
            </div>
        @endif
    </div>

    <!-- Backup & Restore Grid -->
    <div class="grid gap-6 lg:grid-cols-2 text-xs font-semibold">
        <!-- Backup Section -->
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-xs space-y-4">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600">
                    <i class="fas fa-cloud-arrow-down text-xs"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Backup & Restore</h3>
                    <p class="text-[11px] font-normal text-slate-500">Generate full database export ZIP archive.</p>
                </div>
            </div>

            <p class="text-xs text-slate-500 font-normal leading-relaxed">
                Generate a complete data snapshot of database tables, product catalog, customer accounts, and order history packed into a ZIP archive.
            </p>

            <div class="space-y-2 pt-2">
                <div class="flex items-center gap-3 rounded-lg bg-slate-50 p-3 border border-slate-100">
                    <i class="fas fa-database text-emerald-500"></i>
                    <span class="font-bold text-slate-800">Database Schema & Data Included</span>
                </div>
            </div>

            <div class="pt-3">
                <button wire:click="downloadBackup" wire:loading.attr="disabled" class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-slate-900 px-4 py-2.5 text-xs font-semibold text-white hover:bg-slate-800 transition-colors shadow-xs disabled:opacity-50">
                    <i class="fas fa-download text-xs"></i>
                    <span>Generate & Download Backup</span>
                </button>
            </div>
        </div>

        <!-- Restore Section -->
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-xs space-y-4">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-rose-50 text-rose-600">
                    <i class="fas fa-cloud-arrow-up text-xs"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Restore System</h3>
                    <p class="text-[11px] font-normal text-slate-500">Restore database state from a backup ZIP.</p>
                </div>
            </div>

            <div class="rounded-lg bg-rose-50 border border-rose-100 p-3 text-xs text-rose-700 font-medium leading-relaxed">
                <i class="fas fa-triangle-exclamation text-rose-500 mr-1.5"></i> Restoring data will overwrite all current database tables with the archive file.
            </div>

            <form wire:submit.prevent="restoreBackup" class="space-y-4">
                <div 
                    x-data="{ isUploading: false, progress: 0 }"
                    x-on:livewire-upload-start="isUploading = true"
                    x-on:livewire-upload-finish="isUploading = false"
                    x-on:livewire-upload-error="isUploading = false"
                    x-on:livewire-upload-progress="progress = $event.detail.progress"
                >
                    <label class="flex flex-col items-center justify-center w-full h-28 border-2 border-dashed border-slate-200 rounded-lg bg-slate-50/50 cursor-pointer hover:bg-slate-50 hover:border-slate-400 transition-colors">
                        <div class="flex flex-col items-center justify-center py-4 text-center">
                            <i class="fas fa-file-zipper text-base text-slate-400 mb-1"></i>
                            <p class="text-xs font-bold text-slate-900">Select Backup ZIP File</p>
                            <p class="text-[10px] text-slate-400 font-normal">Max size: 50MB</p>
                        </div>
                        <input wire:model="backupFile" type="file" class="hidden" accept=".zip" />
                    </label>

                    @if ($backupFile)
                        <div class="mt-2 flex items-center justify-between rounded-lg bg-emerald-50 border border-emerald-100 p-2.5">
                            <span class="text-xs font-bold text-emerald-700 truncate max-w-[200px]">{{ $backupFile->getClientOriginalName() }}</span>
                            <button type="button" wire:click="$set('backupFile', null)" class="text-rose-500 hover:text-rose-700"><i class="fas fa-times text-xs"></i></button>
                        </div>
                    @endif
                    @error('backupFile') <p class="mt-1 text-xs font-semibold text-rose-500">{{ $message }}</p> @error
                </div>

                <button 
                    type="submit" 
                    onclick="return confirm('Restore this backup? Current database data will be replaced.')"
                    class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-rose-600 px-4 py-2.5 text-xs font-semibold text-white hover:bg-rose-700 transition-colors shadow-xs disabled:opacity-50"
                    {{ !$backupFile ? 'disabled' : '' }}
                >
                    <i class="fas fa-rotate-left text-xs"></i>
                    <span>Execute Restore</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Maintenance Utilities -->
    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-xs space-y-4 text-xs font-semibold">
        <h4 class="font-bold text-slate-900 text-sm border-b border-slate-100 pb-2">Maintenance Utilities</h4>
        <div class="grid gap-3 sm:grid-cols-3">
            @foreach([
                ['label' => 'Cache Cleanup', 'desc' => 'Flush system & config cache', 'icon' => 'fa-broom'],
                ['label' => 'Route Optimization', 'desc' => 'Rebuild route cache matrix', 'icon' => 'fa-route'],
                ['label' => 'View Compiler', 'desc' => 'Recompile blade templates', 'icon' => 'fa-paint-brush'],
            ] as $util)
                <div class="flex items-center gap-3 rounded-lg border border-slate-100 bg-slate-50/50 p-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-md bg-white text-slate-600 shadow-xs"><i class="fas {{ $util['icon'] }} text-xs"></i></div>
                    <div>
                        <p class="font-bold text-slate-900">{{ $util['label'] }}</p>
                        <p class="text-[10px] font-normal text-slate-400">{{ $util['desc'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

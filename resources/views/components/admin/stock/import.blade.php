@props(['importFile' => null])
<div class="rounded-[3rem] border border-slate-200 bg-white p-10 shadow-sm relative overflow-hidden">
    <div class="absolute right-0 top-0 -mr-16 -mt-16 h-64 w-64 rounded-full bg-slate-50 opacity-50"></div>

    <div class="relative z-10">
        <div class="flex items-center gap-6 mb-12">
            <div class="flex h-16 w-16 items-center justify-center rounded-[1.5rem] bg-indigo-600 text-white shadow-2xl shadow-indigo-100">
                <i class="fas fa-file-csv text-2xl"></i>
            </div>
            <div>
                <p class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400 leading-none mb-2">Dataset: Synchronization</p>
                <h3 class="text-3xl font-black tracking-tight text-slate-900">Bulk Registry Import</h3>
                <p class="mt-2 text-sm font-medium text-slate-500 max-w-xl">Synchronize large-scale inventory datasets via CSV protocol. Effortlessly map hundreds of product records into the registry in a single transmission.</p>
            </div>
        </div>

        <div class="rounded-[3rem] bg-slate-50/50 border border-slate-100 p-12">
            <div x-data="{ dragging: false }" 
                 x-on:dragover.prevent="dragging = true" 
                 x-on:dragleave.prevent="dragging = false" 
                 x-on:drop.prevent="dragging = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change'))"
                 class="relative flex flex-col items-center justify-center rounded-[2.5rem] border-2 border-dashed bg-white p-16 transition-all group overflow-hidden"
                 :class="dragging ? 'border-indigo-600 bg-indigo-50/50 scale-[0.99]' : 'border-slate-200 hover:border-slate-400 hover:shadow-2xl hover:shadow-slate-100'">
                 
                 <!-- Animated Background Signal -->
                 <div class="absolute inset-0 opacity-0 group-hover:opacity-10 pointer-events-none transition-opacity">
                    <div class="grid grid-cols-8 gap-4 p-8">
                        @for($i=0; $i<32; $i++)
                            <div class="h-4 rounded bg-slate-900"></div>
                        @endfor
                    </div>
                 </div>

                 <div class="relative z-10 flex flex-col items-center">
                    <div class="mb-8 flex h-24 w-24 items-center justify-center rounded-[2rem] bg-slate-50 text-slate-300 transition-all group-hover:bg-slate-900 group-hover:text-white group-hover:rotate-6">
                        <i class="fas fa-cloud-arrow-up text-4xl"></i>
                    </div>
                    <h4 class="text-xl font-black text-slate-900 tracking-tight">Transmit CSV Dataset</h4>
                    <p class="mt-3 text-xs font-bold text-slate-400 uppercase tracking-widest text-center leading-relaxed">
                        Drag your synchronization file here or <br>
                        <span class="text-indigo-600 font-black cursor-pointer hover:underline">browse local data storage</span>
                    </p>
                 </div>
                 
                 <input x-ref="fileInput" type="file" wire:model="importFile" accept=".csv" class="absolute inset-0 cursor-pointer opacity-0">
            </div>
            
            @if($importFile)
                <div class="mt-10 flex items-center justify-between rounded-[2rem] border border-indigo-100 bg-white p-6 shadow-xl shadow-indigo-100/50 animate-in fade-in slide-in-from-top-4">
                    <div class="flex items-center gap-6">
                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600 font-black text-xs border border-indigo-100">
                            CSV
                        </div>
                        <div>
                            <h5 class="text-sm font-black text-slate-900 tracking-tight">{{ $importFile->getClientOriginalName() }}</h5>
                            <div class="mt-1 flex items-center gap-3">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ round($importFile->getSize() / 1024, 1) }} KB</span>
                                <span class="h-1 w-1 rounded-full bg-slate-200"></span>
                                <span class="text-[10px] font-black text-emerald-600 uppercase tracking-widest">Protocol Validated</span>
                            </div>
                        </div>
                    </div>
                    <button type="button" wire:click="importCsv" class="flex items-center gap-3 rounded-2xl bg-slate-900 px-10 py-4 text-[10px] font-black text-white uppercase tracking-[0.2em] shadow-2xl shadow-slate-200 transition-all hover:scale-[1.02] active:scale-[0.98]">
                        <i class="fas fa-bolt text-[10px] opacity-50"></i>
                        Execute Sync
                    </button>
                </div>
            @endif

            @error('importFile')
                <div class="mt-6 flex items-center justify-center gap-3 rounded-2xl bg-rose-50 p-4 text-[10px] font-black text-rose-500 uppercase tracking-widest border border-rose-100 animate-in shake">
                    <i class="fas fa-triangle-exclamation"></i>
                    <span>{{ $message }}</span>
                </div>
            @enderror
        </div>

        <!-- Documentation & Safety Hub -->
        <div class="mt-12 grid gap-6 md:grid-cols-3">
            @foreach([
                ['Data Mapping', 'fa-map-signs', 'Ensure columns match the registry schema'],
                ['Safety Protocol', 'fa-shield-halved', 'Existing records will be updated via SKU'],
                ['Support Center', 'fa-circle-question', 'Download the official CSV blueprint']
            ] as [$title, $icon, $sub])
                <div class="flex items-start gap-4 p-4">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-100 text-slate-400">
                        <i class="fas {{ $icon }} text-[10px]"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-900 uppercase tracking-widest">{{ $title }}</p>
                        <p class="mt-1 text-[10px] font-medium text-slate-400 leading-relaxed">{{ $sub }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

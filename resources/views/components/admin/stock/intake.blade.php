<div class="rounded-2xl sm:rounded-[3rem] border border-slate-200 bg-white p-5 sm:p-10 shadow-sm relative overflow-hidden">
    <div class="absolute right-0 top-0 -mr-16 -mt-16 h-64 w-64 rounded-full bg-slate-50 opacity-50"></div>
    
    <div class="relative z-10">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 sm:gap-6 mb-8 sm:mb-12">
            <div class="flex h-16 w-16 items-center justify-center rounded-[1.5rem] bg-slate-900 text-white shadow-2xl shadow-slate-200 shrink-0">
                <i class="fas fa-plus text-2xl"></i>
            </div>
            <div>
                <p class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400 leading-none mb-2">Protocol: Registration</p>
                <h3 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900">Inventory Intake Desk</h3>
                <p class="mt-2 text-sm font-medium text-slate-500 max-w-xl">Initiate high-velocity stock registration workflows, orchestrate product mapping, or identify existing assets via scanning.</p>
            </div>
        </div>

        <div class="grid gap-6 md:grid-cols-2">
            <!-- Quick Intake Card -->
            <button type="button" wire:click="startQuickIntake" class="group relative flex flex-col gap-6 sm:gap-8 rounded-2xl sm:rounded-[2.5rem] border border-slate-100 bg-slate-50/50 p-6 sm:p-10 text-left transition-all hover:bg-white hover:border-slate-900 hover:shadow-2xl hover:shadow-slate-200 hover:-translate-y-2">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-900 text-white shadow-xl transition-transform group-hover:scale-110 group-hover:rotate-3">
                    <i class="fas fa-bolt text-xl"></i>
                </div>
                <div>
                    <h4 class="text-xl font-black text-slate-900 tracking-tight">Quick Intake Mode</h4>
                    <p class="mt-3 text-sm font-medium text-slate-500 leading-relaxed">Perfect for rapid registration. Focuses exclusively on essential identity, sourcing, and quantity data for immediate warehouse entry.</p>
                </div>
                <div class="flex items-center gap-2 text-[10px] font-black text-slate-900 uppercase tracking-widest mt-4">
                    <span>Initialize Workflow</span>
                    <i class="fas fa-arrow-right transition-transform group-hover:translate-x-2"></i>
                </div>
            </button>

            <!-- Advanced Workflow Card -->
            <button type="button" wire:click="startAdvancedIntake" class="group relative flex flex-col gap-6 sm:gap-8 rounded-2xl sm:rounded-[2.5rem] border border-slate-100 bg-slate-50/50 p-6 sm:p-10 text-left transition-all hover:bg-white hover:border-indigo-600 hover:shadow-2xl hover:shadow-indigo-100 hover:-translate-y-2">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-600 text-white shadow-xl transition-transform group-hover:scale-110 group-hover:-rotate-3">
                    <i class="fas fa-layer-group text-xl"></i>
                </div>
                <div>
                    <h4 class="text-xl font-black text-slate-900 tracking-tight">Advanced Specification</h4>
                    <p class="mt-3 text-sm font-medium text-slate-500 leading-relaxed">Full product orchestration including high-fidelity media, technical specifications, commercial projections, and storefront mapping overrides.</p>
                </div>
                <div class="flex items-center gap-2 text-[10px] font-black text-indigo-600 uppercase tracking-widest mt-4">
                    <span>Analyze & Register</span>
                    <i class="fas fa-arrow-right transition-transform group-hover:translate-x-2"></i>
                </div>
            </button>
        </div>

        <div class="mt-10 sm:mt-16 pt-10 sm:pt-16 border-t border-slate-100">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] leading-none mb-1">Optical identification</p>
                    <h4 class="text-sm font-black text-slate-900 uppercase tracking-widest">Direct Scan Intake</h4>
                </div>
                <div class="flex items-center gap-2 rounded-xl bg-slate-900 px-3 py-1.5 text-[9px] font-black text-white uppercase tracking-widest">
                    <div class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></div>
                    Scanner Active
                </div>
            </div>
            <div class="flex flex-col sm:relative group">
                <div class="relative w-full">
                    <i class="fas fa-barcode absolute left-6 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-slate-900 transition-colors"></i>
                    <input type="text" wire:model.defer="scanCode" wire:keydown.enter.prevent="processScan" 
                        placeholder="Scan barcode to identify existing records or start new intake..." 
                        class="w-full rounded-[2rem] border-slate-100 bg-slate-50/80 pl-16 pr-6 sm:pr-48 py-5 sm:py-6 text-sm font-bold shadow-inner focus:bg-white focus:border-slate-900 focus:ring-0 transition-all">
                </div>
                <button type="button" wire:click="processScan" class="w-full sm:w-auto mt-3 sm:mt-0 sm:absolute sm:right-3 sm:top-1/2 sm:-translate-y-1/2 rounded-2xl bg-slate-900 px-6 sm:px-10 py-3.5 sm:py-3 text-[10px] font-black text-white uppercase tracking-[0.2em] shadow-2xl shadow-slate-200 transition-all hover:scale-[1.02] active:scale-[0.98] text-center">
                    Scan & Initialize
                </button>
            </div>
            <div class="mt-4 flex flex-wrap items-center gap-4 sm:gap-6 px-4 sm:px-6">
                <div class="flex items-center gap-2">
                    <i class="fas fa-keyboard text-[10px] text-slate-300"></i>
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Manual entry supported</span>
                </div>
                <div class="flex items-center gap-2">
                    <i class="fas fa-history text-[10px] text-slate-300"></i>
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Auto-resume active</span>
                </div>
            </div>
        </div>
    </div>
</div>

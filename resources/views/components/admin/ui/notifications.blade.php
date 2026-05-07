<div 
    x-data="{ 
        notifications: [],
        add(e) {
            const id = Date.now();
            const message = e.detail.message || e.detail;
            const type = e.detail.type || 'success';
            
            this.notifications.push({ id, message, type });
            setTimeout(() => this.remove(id), 5000);
        },
        remove(id) {
            this.notifications = this.notifications.filter(n => n.id !== id);
        }
    }"
    x-on:notify.window="add($event)"
    class="fixed bottom-8 right-8 z-[200] flex flex-col gap-4 w-96 pointer-events-none"
>
    <template x-for="n in notifications" :key="n.id">
        <div 
            x-transition:enter="transition ease-out duration-500"
            x-transition:enter-start="opacity-0 translate-y-8 scale-90"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-90 translate-x-12"
            class="pointer-events-auto group relative overflow-hidden rounded-[2rem] border border-slate-200 bg-white p-6 shadow-2xl"
        >
            <div class="absolute left-0 top-0 h-full w-1.5" :class="{
                'bg-emerald-500': n.type === 'success',
                'bg-rose-500': n.type === 'error',
                'bg-amber-500': n.type === 'warning',
                'bg-indigo-500': n.type === 'info'
            }"></div>

            <div class="flex items-start gap-4">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl shadow-inner" :class="{
                    'bg-emerald-50 text-emerald-600': n.type === 'success',
                    'bg-rose-50 text-rose-600': n.type === 'error',
                    'bg-amber-50 text-amber-600': n.type === 'warning',
                    'bg-indigo-50 text-indigo-600': n.type === 'info'
                }">
                    <i class="fas" :class="{
                        'fa-circle-check': n.type === 'success',
                        'fa-circle-xmark': n.type === 'error',
                        'fa-triangle-exclamation': n.type === 'warning',
                        'fa-circle-info': n.type === 'info'
                    }"></i>
                </div>
                <div class="flex-1 pt-0.5">
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-1" x-text="n.type === 'success' ? 'Protocol Success' : 'System Alert'"></p>
                    <p class="text-[13px] font-bold text-slate-900 leading-relaxed" x-text="n.message"></p>
                </div>
                <button @click="remove(n.id)" class="text-slate-300 hover:text-slate-900 transition-colors">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
            
            <!-- Progress Bar -->
            <div class="absolute bottom-0 left-0 h-0.5 bg-slate-100 w-full">
                <div class="h-full bg-slate-900 transition-all duration-[5000ms] ease-linear w-0" x-init="setTimeout(() => $el.style.width = '100%', 10)"></div>
            </div>
        </div>
    </template>
</div>

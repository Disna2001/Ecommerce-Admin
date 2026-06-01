@props(['paymentReviewOrder', 'paymentDecision' => 'approve'])

<div x-data="{ show: true }" x-show="show" class="fixed inset-0 z-[110] flex items-center justify-center p-4">
    <div @click="$wire.closePaymentModal()" class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>
    <div class="relative w-full max-w-2xl rounded-[2.5rem] bg-white p-10 shadow-2xl border border-slate-200">
        <div class="flex items-center justify-between mb-10">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-600 text-white shadow-xl shadow-emerald-500/20">
                    <i class="fas fa-shield-check text-sm"></i>
                </div>
                <div>
                    <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">Financial Protocol</h3>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Verify Payment Integrity</p>
                </div>
            </div>
            <button @click="$wire.closePaymentModal()" class="h-10 w-10 flex items-center justify-center rounded-xl bg-slate-50 text-slate-400 hover:text-rose-600 shadow-inner">
                <i class="fas fa-xmark text-sm"></i>
            </button>
        </div>

        <div class="space-y-8">
            @if($paymentReviewOrder)
                <div class="p-6 rounded-3xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                    <div>
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Order Registry</p>
                        <h4 class="text-xs font-black text-slate-900 uppercase tracking-widest">#{{ $paymentReviewOrder->order_number }}</h4>
                    </div>
                    @if($paymentReviewOrder->payment_proof_path)
                        <a href="{{ \Illuminate\Support\Facades\Storage::url($paymentReviewOrder->payment_proof_path) }}" target="_blank" class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-white border border-emerald-100 text-emerald-600 text-[9px] font-black uppercase tracking-widest hover:bg-emerald-600 hover:text-white transition-all shadow-sm">
                            <i class="fas fa-image"></i>
                            <span>Open Proof Assets</span>
                        </a>
                    @endif
                </div>
            @endif

            <div class="grid gap-4">
                @foreach([
                    ['approve', 'Authorize Payment', 'Mark registry as paid and clear for fulfillment.', 'fa-circle-check', 'emerald'], 
                    ['reject', 'Decline Proof', 'Flag for correction and notify customer.', 'fa-circle-xmark', 'rose']
                ] as [$value, $title, $description, $icon, $tone])
                    <label class="group relative cursor-pointer">
                        <input type="radio" wire:model.live="paymentDecision" value="{{ $value }}" class="sr-only">
                        <div class="flex items-center gap-5 p-5 rounded-2xl border transition-all duration-300 {{ $paymentDecision === $value ? 'bg-'.$tone.'-50 border-'.$tone.'-200 shadow-inner' : 'bg-white border-slate-100 hover:border-slate-300' }}">
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl transition-colors {{ $paymentDecision === $value ? 'bg-'.$tone.'-600 text-white shadow-lg' : 'bg-slate-50 text-slate-400 group-hover:text-slate-900' }}">
                                <i class="fas {{ $icon }} text-sm"></i>
                            </div>
                            <div>
                                <p class="text-[11px] font-black text-slate-900 uppercase tracking-widest">{{ $title }}</p>
                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter mt-1">{{ $description }}</p>
                            </div>
                            @if($paymentDecision === $value)
                                <div class="ml-auto h-2 w-2 rounded-full bg-{{ $tone }}-600"></div>
                            @endif
                        </div>
                    </label>
                @endforeach
            </div>

            <div class="space-y-4">
                <div class="flex items-center gap-3 px-1">
                    <i class="fas fa-note-sticky text-[10px] text-slate-400"></i>
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Review Narrative</label>
                </div>
                <textarea wire:model="paymentReviewNote" rows="4" placeholder="Enter administrative context for this decision..." class="w-full rounded-2xl border-slate-100 bg-slate-50 px-5 py-4 text-xs font-bold text-slate-900 shadow-inner focus:bg-white focus:ring-0"></textarea>
                @error('paymentReviewNote')<p class="text-[9px] font-bold text-rose-500 uppercase tracking-widest px-1">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center gap-3 pt-4">
                <button type="button" @click="$wire.closePaymentModal()" class="flex-1 h-14 rounded-2xl bg-slate-50 text-slate-400 text-[10px] font-black uppercase tracking-[0.2em] hover:bg-rose-50 hover:text-rose-600 transition-all">Abort</button>
                <button wire:click="verifyPayment" class="flex-[2] h-14 rounded-2xl bg-emerald-600 text-white text-[10px] font-black uppercase tracking-[0.2em] shadow-xl shadow-emerald-500/20 hover:scale-105 transition-transform">Authorize Protocol</button>
            </div>
        </div>
    </div>
</div>

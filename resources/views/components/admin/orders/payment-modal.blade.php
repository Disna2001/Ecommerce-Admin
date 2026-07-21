@props(['paymentReviewOrder', 'paymentDecision' => 'approve'])

<div x-data="{ show: true }" x-show="show" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
    <div class="relative w-full max-w-lg rounded-xl border border-slate-200 bg-white p-6 shadow-xl space-y-6">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-900">Verify Payment</h3>
            <button type="button" @click="$wire.closePaymentModal()" class="text-slate-400 hover:text-slate-600"><i class="fas fa-times"></i></button>
        </div>

        <div class="space-y-4 text-xs">
            @if($paymentReviewOrder)
                <div class="p-3.5 rounded-lg bg-slate-50 border border-slate-100 flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Order</p>
                        <h4 class="font-bold text-slate-900">#{{ $paymentReviewOrder->order_number }}</h4>
                    </div>
                    @if($paymentReviewOrder->payment_proof_path)
                        <a href="{{ \Illuminate\Support\Facades\Storage::url($paymentReviewOrder->payment_proof_path) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-slate-700 text-xs font-semibold hover:bg-slate-50 shadow-xs">
                            <i class="fas fa-image text-slate-400"></i>
                            <span>View Payment Proof</span>
                        </a>
                    @endif
                </div>
            @endif

            <div class="space-y-2">
                <label class="block font-bold text-slate-700">Decision</label>
                <div class="grid gap-2 sm:grid-cols-2">
                    <label class="flex cursor-pointer items-start gap-3 rounded-lg border p-3 transition {{ $paymentDecision === 'approve' ? 'border-emerald-500 bg-emerald-50/50' : 'border-slate-200 bg-white' }}">
                        <input type="radio" wire:model.live="paymentDecision" value="approve" class="sr-only">
                        <div class="flex h-7 w-7 items-center justify-center rounded-md bg-emerald-100 text-emerald-700 shrink-0"><i class="fas fa-check text-xs"></i></div>
                        <div>
                            <p class="font-bold text-slate-900">Approve Payment</p>
                            <p class="text-[11px] text-slate-500 mt-0.5">Mark order as paid and proceed.</p>
                        </div>
                    </label>
                    <label class="flex cursor-pointer items-start gap-3 rounded-lg border p-3 transition {{ $paymentDecision === 'reject' ? 'border-rose-500 bg-rose-50/50' : 'border-slate-200 bg-white' }}">
                        <input type="radio" wire:model.live="paymentDecision" value="reject" class="sr-only">
                        <div class="flex h-7 w-7 items-center justify-center rounded-md bg-rose-100 text-rose-700 shrink-0"><i class="fas fa-times text-xs"></i></div>
                        <div>
                            <p class="font-bold text-slate-900">Decline Payment</p>
                            <p class="text-[11px] text-slate-500 mt-0.5">Request re-submission.</p>
                        </div>
                    </label>
                </div>
            </div>

            <div class="space-y-1">
                <label class="block font-bold text-slate-700">Review Note (Optional)</label>
                <textarea wire:model="paymentReviewNote" rows="3" placeholder="Enter note or reason for customer..." class="w-full rounded-lg border-slate-200 px-3 py-2 font-medium text-slate-900 focus:ring-0"></textarea>
                @error('paymentReviewNote') <span class="text-rose-500">{{ $message }}</span> @enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" @click="$wire.closePaymentModal()" class="rounded-lg border border-slate-200 bg-white px-4 py-2 font-semibold text-slate-700 hover:bg-slate-50">Cancel</button>
                <button wire:click="verifyPayment" class="rounded-lg bg-emerald-600 px-4 py-2 font-semibold text-white hover:bg-emerald-700 shadow-xs">Save Decision</button>
            </div>
        </div>
    </div>
</div>

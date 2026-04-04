<div class="fixed inset-0 z-[95] flex items-center justify-center bg-slate-950/55 p-4 backdrop-blur-sm">
    <div class="w-full max-w-2xl overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-2xl">
        <div class="border-b border-slate-200 px-6 py-5">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Payment Control</p>
                    <h3 class="mt-2 text-xl font-semibold text-slate-900">Review Payment</h3>
                </div>
                <button wire:click="closePaymentModal" class="rounded-full border border-slate-200 bg-slate-50 p-3 text-slate-500 transition hover:bg-slate-100"><i class="fas fa-xmark"></i></button>
            </div>
        </div>
        <div class="space-y-5 p-6">
            @if($paymentReviewOrder)
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <div class="grid gap-3 md:grid-cols-2">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Order</p>
                            <p class="mt-2 text-sm font-semibold text-slate-900">{{ $paymentReviewOrder->order_number }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Reference</p>
                            <p class="mt-2 font-mono text-sm text-slate-700">{{ $paymentReviewOrder->payment_reference ?: 'No reference' }}</p>
                        </div>
                    </div>
                    @if($paymentReviewOrder->payment_proof_path)
                        <a href="{{ \Illuminate\Support\Facades\Storage::url($paymentReviewOrder->payment_proof_path) }}" target="_blank" class="mt-4 inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-white px-4 py-2 text-sm font-semibold text-emerald-700 transition hover:bg-emerald-50"><i class="fas fa-image"></i><span>Open proof image</span></a>
                    @endif
                </div>
            @endif

            <div class="grid gap-3">
                @foreach([['approve', 'Approve payment', 'Marks the order as paid and verified.', 'fa-circle-check', 'emerald'], ['reject', 'Reject proof', 'Keeps payment unpaid and asks the customer to correct it.', 'fa-circle-xmark', 'rose']] as [$value, $title, $description, $icon, $tone])
                    <label class="flex cursor-pointer items-start gap-4 rounded-2xl border-2 p-4 transition {{ $paymentDecision === $value ? ($tone === 'emerald' ? 'border-emerald-300 bg-emerald-50' : 'border-rose-300 bg-rose-50') : 'border-slate-200 bg-white' }}">
                        <input type="radio" wire:model.live="paymentDecision" value="{{ $value }}" class="sr-only">
                        <div class="flex h-10 w-10 items-center justify-center rounded-2xl {{ $tone === 'emerald' ? 'bg-emerald-100 text-emerald-600' : 'bg-rose-100 text-rose-600' }}"><i class="fas {{ $icon }}"></i></div>
                        <div>
                            <p class="text-sm font-semibold text-slate-900">{{ $title }}</p>
                            <p class="mt-1 text-xs leading-6 text-slate-500">{{ $description }}</p>
                        </div>
                    </label>
                @endforeach
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">Review note</label>
                <textarea wire:model="paymentReviewNote" rows="4" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none resize-none" placeholder="Add internal and customer-facing context."></textarea>
                @error('paymentReviewNote')<p class="mt-1 text-xs text-rose-500">{{ $message }}</p>@enderror
            </div>
        </div>
        <div class="flex justify-end gap-3 border-t border-slate-200 bg-slate-50 px-6 py-4">
            <button wire:click="closePaymentModal" class="rounded-full border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-100">Cancel</button>
            <button wire:click="verifyPayment" class="rounded-full bg-emerald-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700">Save review</button>
        </div>
    </div>
</div>

@if($showModal && $viewingReview)
    <div class="fixed inset-0 z-50 flex items-center justify-center px-4">
        <div class="fixed inset-0 bg-slate-950/60" wire:click="$set('showModal', false)"></div>
        <div class="relative z-10 w-full max-w-2xl overflow-hidden rounded-[2rem] border border-white/70 bg-white shadow-2xl dark:border-white/10 dark:bg-slate-950">
            <div class="bg-gradient-to-r from-indigo-600 to-fuchsia-600 px-6 py-5 text-white">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.28em] text-white/60">Review Detail</p>
                        <h3 class="mt-2 text-xl font-bold">Customer feedback</h3>
                        <p class="mt-1 text-sm text-white/70">{{ $viewingReview->created_at->format('F d, Y \a\t H:i') }}</p>
                    </div>
                    <button wire:click="$set('showModal', false)" class="text-white/70 transition hover:text-white"><i class="fas fa-times text-lg"></i></button>
                </div>
            </div>
            <div class="space-y-5 p-6">
                <div class="flex items-center gap-2 text-amber-400">@for($i = 1; $i <= 5; $i++)<i class="fas fa-star text-xl {{ $i <= $viewingReview->rating ? 'opacity-100' : 'opacity-20' }}"></i>@endfor<span class="ml-2 text-base font-semibold text-slate-700 dark:text-slate-200">{{ $viewingReview->rating }}/5</span></div>
                <div class="grid gap-4 md:grid-cols-2">
                    <div class="rounded-3xl bg-slate-50 p-4 dark:bg-slate-900"><p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Reviewer</p><p class="mt-3 text-sm font-semibold text-slate-900 dark:text-white">{{ $viewingReview->user?->name ?? 'Unknown reviewer' }}</p><p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ $viewingReview->user?->email }}</p></div>
                    <div class="rounded-3xl bg-slate-50 p-4 dark:bg-slate-900"><p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Product</p><p class="mt-3 text-sm font-semibold text-slate-900 dark:text-white">{{ $viewingReview->stock?->name ?? 'Unknown product' }}</p>@if($viewingReview->order)<p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Order #{{ $viewingReview->order->order_number }}</p>@endif</div>
                </div>
                <div class="rounded-3xl bg-slate-50 p-5 dark:bg-slate-900">@if($viewingReview->title)<h4 class="text-lg font-bold text-slate-900 dark:text-white">{{ $viewingReview->title }}</h4>@endif<p class="mt-3 text-sm leading-7 text-slate-600 dark:text-slate-300">{{ $viewingReview->body }}</p></div>
                <div class="flex flex-wrap items-center gap-2"><span class="rounded-full px-3 py-1 text-xs font-semibold {{ $viewingReview->is_approved ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-400/10 dark:text-emerald-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-400/10 dark:text-amber-300' }}">{{ $viewingReview->is_approved ? 'Published' : 'Pending approval' }}</span>@if($viewingReview->is_flagged)<span class="rounded-full bg-rose-100 px-3 py-1 text-xs font-semibold text-rose-700 dark:bg-rose-400/10 dark:text-rose-300">Flagged for review</span>@endif</div>
            </div>
            <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 bg-slate-50 px-6 py-4 dark:border-slate-800 dark:bg-slate-900/70">
                <div class="flex flex-wrap gap-2">@if(!$viewingReview->is_approved)<button wire:click="approve({{ $viewingReview->id }}); $set('showModal', false)" class="rounded-2xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700">Approve</button>@else<button wire:click="reject({{ $viewingReview->id }}); $set('showModal', false)" class="rounded-2xl bg-slate-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-slate-200 dark:text-slate-900 dark:hover:bg-white">Unpublish</button>@endif<button wire:click="openEdit({{ $viewingReview->id }}); $set('showModal', false)" class="rounded-2xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700">Edit</button><button wire:click="delete({{ $viewingReview->id }})" wire:confirm="Delete this review permanently?" class="rounded-2xl bg-rose-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-rose-700">Delete</button></div>
                <button wire:click="$set('showModal', false)" class="rounded-2xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-white dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-950">Close</button>
            </div>
        </div>
    </div>
@endif

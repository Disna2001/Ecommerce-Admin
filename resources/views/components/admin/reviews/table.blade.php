<x-admin.ui.panel title="Review Queue" description="Keep moderation focused on the highest-signal reviews instead of a crowded table." padding="p-0">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800">
            <thead class="bg-slate-50 dark:bg-slate-900/70">
                <tr>
                    <th class="px-4 py-3 w-10"><input type="checkbox" wire:model.live="selectAll" class="rounded border-slate-300 text-indigo-600"></th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400"><button wire:click="sortBy('rating')" class="flex items-center gap-2"><span>Rating</span><i class="fas fa-sort{{ $sortField === 'rating' ? ($sortDir === 'asc' ? '-up' : '-down') : '' }} text-[10px] opacity-50"></i></button></th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Reviewer</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Product</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Review</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400"><button wire:click="sortBy('created_at')" class="flex items-center gap-2"><span>Date</span><i class="fas fa-sort{{ $sortField === 'created_at' ? ($sortDir === 'asc' ? '-up' : '-down') : '' }} text-[10px] opacity-50"></i></button></th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white dark:divide-slate-900 dark:bg-slate-950/60">
                @forelse($reviews as $review)
                    <tr class="align-top transition hover:bg-slate-50/80 dark:hover:bg-slate-900/50 {{ $review->is_flagged ? 'bg-rose-50/70 dark:bg-rose-400/5' : '' }}">
                        <td class="px-4 py-4"><input type="checkbox" wire:model.live="selected" value="{{ $review->id }}" class="rounded border-slate-300 text-indigo-600"></td>
                        <td class="px-4 py-4"><div class="flex items-center gap-1 text-amber-400">@for($i = 1; $i <= 5; $i++)<i class="fas fa-star text-xs {{ $i <= $review->rating ? 'opacity-100' : 'opacity-20' }}"></i>@endfor</div><p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $review->rating }}/5</p></td>
                        <td class="px-4 py-4"><div class="flex items-center gap-3"><div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-indigo-600 text-xs font-bold text-white">{{ strtoupper(substr($review->user?->name ?? 'U', 0, 1)) }}</div><div><p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $review->user?->name ?? 'Unknown' }}</p><p class="text-xs text-slate-400">{{ $review->user?->email }}</p></div></div></td>
                        <td class="px-4 py-4"><div class="flex items-center gap-3">@if($review->stock && !empty($review->stock->images))<img src="{{ \Illuminate\Support\Facades\Storage::url($review->stock->images[0]) }}" class="h-10 w-10 rounded-2xl object-cover">@else<div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 dark:bg-slate-900"><i class="fas fa-box text-xs"></i></div>@endif<div><p class="text-sm font-medium text-slate-800 dark:text-slate-200">{{ $review->stock?->name ?? 'Unavailable product' }}</p>@if($review->order)<p class="text-xs text-slate-400">Order #{{ $review->order->order_number }}</p>@endif</div></div></td>
                        <td class="px-4 py-4">@if($review->title)<p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $review->title }}</p>@endif<p class="mt-1 max-w-xs text-xs leading-5 text-slate-500 dark:text-slate-400">{{ \Illuminate\Support\Str::limit($review->body, 90) }}</p></td>
                        <td class="px-4 py-4 text-xs text-slate-500 dark:text-slate-400"><p>{{ $review->created_at->format('M d, Y') }}</p><p class="mt-1">{{ $review->created_at->diffForHumans() }}</p></td>
                        <td class="px-4 py-4"><div class="flex flex-col gap-2"><span class="inline-flex w-fit items-center rounded-full px-3 py-1 text-xs font-semibold {{ $review->is_approved ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-400/10 dark:text-emerald-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-400/10 dark:text-amber-300' }}">{{ $review->is_approved ? 'Published' : 'Pending' }}</span>@if($review->is_flagged)<span class="inline-flex w-fit items-center rounded-full bg-rose-100 px-3 py-1 text-xs font-semibold text-rose-700 dark:bg-rose-400/10 dark:text-rose-300">Flagged</span>@endif</div></td>
                        <td class="px-4 py-4"><div class="flex flex-wrap items-center gap-2"><button wire:click="viewReview({{ $review->id }})" class="rounded-2xl bg-indigo-50 px-3 py-2 text-xs font-semibold text-indigo-600 transition hover:bg-indigo-100 dark:bg-indigo-400/10 dark:text-indigo-300">View</button><button wire:click="openEdit({{ $review->id }})" class="rounded-2xl bg-sky-50 px-3 py-2 text-xs font-semibold text-sky-600 transition hover:bg-sky-100 dark:bg-sky-400/10 dark:text-sky-300">Edit</button>@if(!$review->is_approved)<button wire:click="approve({{ $review->id }})" class="rounded-2xl bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-600 transition hover:bg-emerald-100 dark:bg-emerald-400/10 dark:text-emerald-300">Approve</button>@else<button wire:click="reject({{ $review->id }})" class="rounded-2xl bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-200 dark:bg-slate-900 dark:text-slate-300">Unpublish</button>@endif<button wire:click="toggleFlag({{ $review->id }})" class="rounded-2xl bg-amber-50 px-3 py-2 text-xs font-semibold text-amber-600 transition hover:bg-amber-100 dark:bg-amber-400/10 dark:text-amber-300">{{ $review->is_flagged ? 'Unflag' : 'Flag' }}</button><button wire:click="delete({{ $review->id }})" wire:confirm="Delete this review permanently?" class="rounded-2xl bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-600 transition hover:bg-rose-100 dark:bg-rose-400/10 dark:text-rose-300">Delete</button></div></td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="px-6 py-16"><x-admin.ui.empty-state title="No reviews found" description="Reviews from customers will appear here when products start receiving feedback." /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="border-t border-slate-200 px-6 py-4 dark:border-slate-800">{{ $reviews->links() }}</div>
</x-admin.ui.panel>

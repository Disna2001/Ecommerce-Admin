<div>

{{-- Toast --}}
<div x-data="{ show:false, message:'', type:'success' }"
     x-on:notify.window="show=true; message=$event.detail.message; type=$event.detail.type; setTimeout(()=>show=false,3500)"
     x-show="show" x-transition
     class="fixed bottom-5 right-5 z-50 px-5 py-3 rounded-2xl shadow-xl text-sm font-semibold text-white flex items-center gap-2"
     :class="type==='success'?'bg-green-500':(type==='error'?'bg-red-500':'bg-indigo-500')"
     style="display:none">
    <i class="fas fa-check-circle"></i><span x-text="message"></span>
</div>

<div class="p-6 max-w-full">

    {{-- PAGE HEADER --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Review Manager</h1>
            <p class="text-gray-500 text-sm mt-1">Moderate, approve and manage all customer reviews</p>
        </div>
    </div>

    {{-- STATS CARDS --}}
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3 mb-6">
        @php
        $stats = $this->stats;
        $statCards = [
            ['Total',    $stats['total'],    '#6b7280', '#f9fafb', 'fa-comment-alt'],
            ['Approved', $stats['approved'],  '#10b981', '#d1fae5', 'fa-check-circle'],
            ['Pending',  $stats['pending'],   '#f59e0b', '#fef3c7', 'fa-clock'],
            ['Flagged',  $stats['flagged'],   '#ef4444', '#fee2e2', 'fa-flag'],
            ['Avg Score',$stats['avg'].'★',  '#8b5cf6', '#ede9fe', 'fa-star'],
            ['5 Stars',  $stats['five'],      '#f59e0b', '#fef9c3', 'fa-star'],
            ['1 Star',   $stats['one'],       '#ef4444', '#fee2e2', 'fa-star'],
        ];
        @endphp
        @foreach($statCards as [$label, $value, $color, $bg, $icon])
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
            <div class="w-8 h-8 rounded-xl flex items-center justify-center mb-2" style="background:{{ $bg }}">
                <i class="fas {{ $icon }} text-sm" style="color:{{ $color }}"></i>
            </div>
            <p class="text-lg font-extrabold text-gray-900">{{ $value }}</p>
            <p class="text-xs text-gray-500">{{ $label }}</p>
        </div>
        @endforeach
    </div>

    {{-- FILTERS --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-5">
        <div class="flex flex-wrap items-center gap-3">
            {{-- Search --}}
            <div class="relative flex-1 min-w-48">
                <input type="text" wire:model.live.debounce.400ms="search"
                       placeholder="Search by reviewer, product, content..."
                       class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-indigo-400">
                <i class="fas fa-search absolute left-3 top-2.5 text-gray-300 text-xs"></i>
                <div wire:loading wire:target="search" class="absolute right-3 top-2.5">
                    <i class="fas fa-spinner fa-spin text-gray-300 text-xs"></i>
                </div>
            </div>

            {{-- Status filter --}}
            <select wire:model.live="filterStatus"
                    class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-indigo-400 bg-white">
                <option value="">All Statuses</option>
                <option value="approved">Approved</option>
                <option value="pending">Pending</option>
                <option value="flagged">Flagged</option>
            </select>

            {{-- Rating filter --}}
            <select wire:model.live="filterRating"
                    class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-indigo-400 bg-white">
                <option value="">All Ratings</option>
                @for($r=5; $r>=1; $r--)
                <option value="{{ $r }}">{{ str_repeat('★',$r) }}{{ str_repeat('☆',5-$r) }} {{ $r }} star{{ $r>1?'s':'' }}</option>
                @endfor
            </select>

            {{-- Per page --}}
            <select wire:model.live="perPage"
                    class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none bg-white">
                <option value="15">15/page</option>
                <option value="25">25/page</option>
                <option value="50">50/page</option>
            </select>

            {{-- Clear --}}
            @if($search || $filterStatus || $filterRating)
            <button wire:click="$set('search',''); $set('filterStatus',''); $set('filterRating','')"
                    class="px-3 py-2 text-xs font-semibold text-red-500 border border-red-200 rounded-xl hover:bg-red-50 transition">
                <i class="fas fa-times mr-1"></i>Clear
            </button>
            @endif
        </div>

        {{-- Bulk actions (shown when items selected) --}}
        @if(count($selected) > 0)
        <div class="mt-3 flex items-center gap-3 pt-3 border-t border-gray-100">
            <span class="text-sm font-semibold text-gray-700">
                {{ count($selected) }} selected
            </span>
            <button wire:click="bulkApprove"
                    wire:loading.attr="disabled"
                    class="px-4 py-1.5 bg-green-500 text-white rounded-lg text-xs font-semibold hover:bg-green-600 transition flex items-center gap-1.5">
                <span wire:loading.remove wire:target="bulkApprove"><i class="fas fa-check mr-1"></i>Approve All</span>
                <span wire:loading wire:target="bulkApprove"><i class="fas fa-spinner fa-spin"></i></span>
            </button>
            <button wire:click="bulkReject"
                    class="px-4 py-1.5 bg-gray-500 text-white rounded-lg text-xs font-semibold hover:bg-gray-600 transition">
                <i class="fas fa-eye-slash mr-1"></i>Unpublish All
            </button>
            <button wire:click="bulkDelete"
                    wire:confirm="Delete all {{ count($selected) }} selected reviews? This cannot be undone."
                    class="px-4 py-1.5 bg-red-500 text-white rounded-lg text-xs font-semibold hover:bg-red-600 transition">
                <i class="fas fa-trash mr-1"></i>Delete All
            </button>
        </div>
        @endif
    </div>

    {{-- REVIEWS TABLE --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden" wire:loading.class="opacity-60">
        <table class="min-w-full">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-4 py-3 w-8">
                        <input type="checkbox" wire:model.live="selectAll"
                               class="rounded border-gray-300 text-indigo-600 cursor-pointer">
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                        <button wire:click="sortBy('rating')" class="flex items-center gap-1 hover:text-gray-800">
                            Rating
                            <i class="fas fa-sort{{ $sortField==='rating' ? ($sortDir==='asc'?'-up':'-down') : '' }} text-xs opacity-40"></i>
                        </button>
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Reviewer</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Product</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Review</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide w-24">
                        <button wire:click="sortBy('created_at')" class="flex items-center gap-1 hover:text-gray-800">
                            Date
                            <i class="fas fa-sort{{ $sortField==='created_at' ? ($sortDir==='asc'?'-up':'-down') : '' }} text-xs opacity-40"></i>
                        </button>
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide w-28">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide w-36">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($reviews as $review)
                <tr class="hover:bg-gray-50 transition {{ $review->is_flagged ? 'bg-red-50' : '' }}">
                    {{-- Checkbox --}}
                    <td class="px-4 py-3">
                        <input type="checkbox" wire:model.live="selected" value="{{ $review->id }}"
                               class="rounded border-gray-300 text-indigo-600 cursor-pointer">
                    </td>

                    {{-- Stars --}}
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-0.5">
                            @for($i=1; $i<=5; $i++)
                            <i class="fas fa-star text-xs {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-200' }}"></i>
                            @endfor
                        </div>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $review->rating }}/5</p>
                    </td>

                    {{-- Reviewer --}}
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                                 style="background:#4f46e5">
                                {{ strtoupper(substr($review->user?->name ?? 'U', 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $review->user?->name ?? 'Unknown' }}</p>
                                <p class="text-xs text-gray-400 truncate max-w-28">{{ $review->user?->email }}</p>
                            </div>
                        </div>
                    </td>

                    {{-- Product --}}
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            @if($review->stock && !empty($review->stock->images))
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($review->stock->images[0]) }}"
                                 class="w-9 h-9 rounded-lg object-cover flex-shrink-0">
                            @else
                            <div class="w-9 h-9 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-box text-gray-300 text-xs"></i>
                            </div>
                            @endif
                            <p class="text-sm text-gray-700 truncate max-w-32">{{ $review->stock?->name ?? '—' }}</p>
                        </div>
                    </td>

                    {{-- Review content --}}
                    <td class="px-4 py-3">
                        @if($review->title)
                        <p class="text-sm font-semibold text-gray-900 truncate max-w-xs">{{ $review->title }}</p>
                        @endif
                        <p class="text-xs text-gray-500 truncate max-w-xs mt-0.5">{{ \Illuminate\Support\Str::limit($review->body, 80) }}</p>
                    </td>

                    {{-- Date --}}
                    <td class="px-4 py-3 text-xs text-gray-500 whitespace-nowrap">
                        {{ $review->created_at->format('M d, Y') }}<br>
                        <span class="text-gray-400">{{ $review->created_at->diffForHumans() }}</span>
                    </td>

                    {{-- Status badges --}}
                    <td class="px-4 py-3">
                        <div class="flex flex-col gap-1">
                            @if($review->is_approved)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                <i class="fas fa-check text-xs"></i> Published
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                                <i class="fas fa-clock text-xs"></i> Pending
                            </span>
                            @endif
                            @if($review->is_flagged)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-600">
                                <i class="fas fa-flag text-xs"></i> Flagged
                            </span>
                            @endif
                        </div>
                    </td>

                    {{-- Actions --}}
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-1.5 flex-wrap">
                            {{-- View --}}
                            <button wire:click="viewReview({{ $review->id }})"
                                    title="View full review"
                                    class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 flex items-center justify-center transition">
                                <i class="fas fa-eye text-xs"></i>
                            </button>
                            {{-- Edit --}}
                            <button wire:click="openEdit({{ $review->id }})"
                                    title="Edit review"
                                    class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 flex items-center justify-center transition">
                                <i class="fas fa-edit text-xs"></i>
                            </button>
                            {{-- Approve / Unpublish --}}
                            @if(!$review->is_approved)
                            <button wire:click="approve({{ $review->id }})"
                                    title="Approve"
                                    class="w-7 h-7 rounded-lg bg-green-50 text-green-600 hover:bg-green-100 flex items-center justify-center transition">
                                <i class="fas fa-check text-xs"></i>
                            </button>
                            @else
                            <button wire:click="reject({{ $review->id }})"
                                    title="Unpublish"
                                    class="w-7 h-7 rounded-lg bg-gray-100 text-gray-500 hover:bg-gray-200 flex items-center justify-center transition">
                                <i class="fas fa-eye-slash text-xs"></i>
                            </button>
                            @endif
                            {{-- Flag --}}
                            <button wire:click="toggleFlag({{ $review->id }})"
                                    title="{{ $review->is_flagged ? 'Remove flag' : 'Flag review' }}"
                                    class="w-7 h-7 rounded-lg flex items-center justify-center transition {{ $review->is_flagged ? 'bg-red-100 text-red-500 hover:bg-red-200' : 'bg-orange-50 text-orange-400 hover:bg-orange-100' }}">
                                <i class="fas fa-flag text-xs"></i>
                            </button>
                            {{-- Delete --}}
                            <button wire:click="delete({{ $review->id }})"
                                    wire:confirm="Delete this review permanently?"
                                    title="Delete"
                                    class="w-7 h-7 rounded-lg bg-red-50 text-red-500 hover:bg-red-100 flex items-center justify-center transition">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-16 text-center text-gray-400">
                        <i class="fas fa-comment-slash text-5xl block mb-3 opacity-20"></i>
                        <p class="font-medium text-gray-600">No reviews found</p>
                        <p class="text-sm mt-1">Reviews from customers will appear here</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $reviews->links() }}
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════
         VIEW REVIEW MODAL
    ═══════════════════════════════════════════════ --}}
    @if($showModal && $viewingReview)
    <div class="fixed inset-0 z-50 flex items-center justify-center px-4">
        <div class="fixed inset-0 bg-black/50" wire:click="$set('showModal', false)"></div>
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg z-50 overflow-hidden">

            {{-- Header --}}
            <div class="px-6 py-5 border-b border-gray-100 flex items-start justify-between"
                 style="background:linear-gradient(135deg,#4f46e5,#7c3aed)">
                <div class="text-white">
                    <h3 class="text-lg font-bold">Review Details</h3>
                    <p class="text-white/70 text-sm">{{ $viewingReview->created_at->format('F d, Y \a\t H:i') }}</p>
                </div>
                <button wire:click="$set('showModal', false)" class="text-white/70 hover:text-white mt-1">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div class="p-6 space-y-5">
                {{-- Stars --}}
                <div class="flex items-center gap-2">
                    @for($i=1; $i<=5; $i++)
                    <i class="fas fa-star text-xl {{ $i <= $viewingReview->rating ? 'text-yellow-400' : 'text-gray-200' }}"></i>
                    @endfor
                    <span class="text-lg font-bold text-gray-700 ml-1">{{ $viewingReview->rating }}/5</span>
                </div>

                {{-- Product --}}
                <div class="flex items-center gap-3 bg-gray-50 rounded-2xl p-3">
                    @if($viewingReview->stock && !empty($viewingReview->stock->images))
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($viewingReview->stock->images[0]) }}"
                         class="w-14 h-14 rounded-xl object-cover flex-shrink-0">
                    @else
                    <div class="w-14 h-14 rounded-xl bg-gray-200 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-box text-gray-400 text-xl"></i>
                    </div>
                    @endif
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">Product</p>
                        <p class="font-semibold text-gray-900">{{ $viewingReview->stock?->name ?? 'Unknown product' }}</p>
                        @if($viewingReview->order)
                        <p class="text-xs text-indigo-500">Order #{{ $viewingReview->order->order_number }}</p>
                        @endif
                    </div>
                </div>

                {{-- Reviewer --}}
                <div class="flex items-center gap-3 bg-gray-50 rounded-2xl p-3">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0"
                         style="background:#4f46e5">
                        {{ strtoupper(substr($viewingReview->user?->name ?? 'U', 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">Reviewer</p>
                        <p class="font-semibold text-gray-900">{{ $viewingReview->user?->name }}</p>
                        <p class="text-xs text-gray-400">{{ $viewingReview->user?->email }}</p>
                    </div>
                </div>

                {{-- Review content --}}
                <div>
                    @if($viewingReview->title)
                    <h4 class="font-bold text-gray-900 text-base mb-2">{{ $viewingReview->title }}</h4>
                    @endif
                    <p class="text-gray-700 leading-relaxed text-sm bg-gray-50 rounded-2xl p-4">{{ $viewingReview->body }}</p>
                </div>

                {{-- Status tags --}}
                <div class="flex items-center gap-2 flex-wrap">
                    @if($viewingReview->is_approved)
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">
                        <i class="fas fa-check mr-1"></i>Published
                    </span>
                    @else
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-700">
                        <i class="fas fa-clock mr-1"></i>Pending Approval
                    </span>
                    @endif
                    @if($viewingReview->is_flagged)
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-600">
                        <i class="fas fa-flag mr-1"></i>Flagged for review
                    </span>
                    @endif
                </div>
            </div>

            {{-- Footer actions --}}
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex items-center justify-between gap-2 flex-wrap">
                <div class="flex gap-2">
                    @if(!$viewingReview->is_approved)
                    <button wire:click="approve({{ $viewingReview->id }}); $set('showModal', false)"
                            class="px-4 py-2 bg-green-500 text-white rounded-xl text-sm font-semibold hover:bg-green-600 transition">
                        <i class="fas fa-check mr-1"></i>Approve
                    </button>
                    @else
                    <button wire:click="reject({{ $viewingReview->id }}); $set('showModal', false)"
                            class="px-4 py-2 bg-gray-500 text-white rounded-xl text-sm font-semibold hover:bg-gray-600 transition">
                        <i class="fas fa-eye-slash mr-1"></i>Unpublish
                    </button>
                    @endif
                    <button wire:click="openEdit({{ $viewingReview->id }}); $set('showModal', false)"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-xl text-sm font-semibold hover:bg-indigo-700 transition">
                        <i class="fas fa-edit mr-1"></i>Edit
                    </button>
                    <button wire:click="delete({{ $viewingReview->id }})"
                            wire:confirm="Delete this review permanently?"
                            class="px-4 py-2 bg-red-500 text-white rounded-xl text-sm font-semibold hover:bg-red-600 transition">
                        <i class="fas fa-trash mr-1"></i>Delete
                    </button>
                </div>
                <button wire:click="$set('showModal', false)"
                        class="px-4 py-2 border border-gray-200 text-gray-600 rounded-xl text-sm hover:bg-gray-100 transition">
                    Close
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════
         EDIT REVIEW MODAL
    ═══════════════════════════════════════════════ --}}
    @if($showEditModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center px-4">
        <div class="fixed inset-0 bg-black/50" wire:click="$set('showEditModal', false)"></div>
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg z-50 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="font-bold text-gray-900 text-lg"><i class="fas fa-edit mr-2 text-indigo-500"></i>Edit Review</h3>
                <button wire:click="$set('showEditModal', false)" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-6 space-y-4">
                {{-- Star picker --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-2 uppercase tracking-wide">Rating *</label>
                    <div class="flex items-center gap-2">
                        @for($i=1; $i<=5; $i++)
                        <button type="button" wire:click="$set('editRating', {{ $i }})"
                                class="text-3xl transition hover:scale-110"
                                style="color:{{ $editRating >= $i ? '#f59e0b' : '#d1d5db' }}">★</button>
                        @endfor
                        <span class="text-sm text-gray-500 ml-2">
                            {{ ['','Poor','Fair','Good','Very Good','Excellent'][$editRating] ?? '' }}
                        </span>
                    </div>
                </div>

                {{-- Title --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Title</label>
                    <input wire:model="editTitle" type="text" placeholder="Review headline..."
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-indigo-400">
                    @error('editTitle')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Body --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Review Content *</label>
                    <textarea wire:model="editBody" rows="5"
                              class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm resize-none focus:outline-none focus:border-indigo-400"></textarea>
                    @error('editBody')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Approval toggle --}}
                <div class="flex items-center justify-between py-3 px-4 bg-gray-50 rounded-xl">
                    <div>
                        <p class="text-sm font-semibold text-gray-900">Publish Review</p>
                        <p class="text-xs text-gray-400">Visible to customers on the product page</p>
                    </div>
                    <button wire:click="$toggle('editApproved')"
                            class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors duration-200"
                            style="{{ $editApproved ? 'background:#4f46e5' : 'background:#e5e7eb' }}">
                        <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform duration-200"
                              style="{{ $editApproved ? 'transform:translateX(20px)' : 'transform:translateX(4px)' }}"></span>
                    </button>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-2">
                <button wire:click="$set('showEditModal', false)"
                        class="px-5 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-600 hover:bg-gray-50 transition">Cancel</button>
                <button wire:click="saveEdit"
                        wire:loading.attr="disabled"
                        class="px-5 py-2.5 bg-indigo-600 text-white rounded-xl text-sm font-semibold hover:bg-indigo-700 transition flex items-center gap-2">
                    <span wire:loading.remove wire:target="saveEdit"><i class="fas fa-save mr-1"></i>Save Changes</span>
                    <span wire:loading wire:target="saveEdit"><i class="fas fa-spinner fa-spin mr-1"></i>Saving...</span>
                </button>
            </div>
        </div>
    </div>
    @endif

</div>
</div>
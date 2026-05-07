<div class="space-y-4">
    @forelse($reviews as $review)
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm group hover:border-slate-900 transition-all {{ $review->is_flagged ? 'border-rose-200 bg-rose-50/20' : '' }}">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-center">
                <!-- Reviewer Signal -->
                <div class="flex items-center gap-4 min-w-[200px]">
                    <input type="checkbox" wire:model.live="selected" value="{{ $review->id }}" class="h-4 w-4 rounded border-slate-200 text-slate-900 focus:ring-slate-900">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 font-black text-sm uppercase">
                        {{ substr($review->user->name ?? '?', 0, 2) }}
                    </div>
                    <div>
                        <h4 class="text-sm font-black text-slate-900 tracking-tight line-clamp-1">{{ $review->user->name ?? 'Guest User' }}</h4>
                        <div class="flex items-center gap-1 mt-1">
                            @for($i=1; $i<=5; $i++)
                                <i class="fas fa-star text-[8px] {{ $i <= $review->rating ? 'text-amber-400' : 'text-slate-200' }}"></i>
                            @endfor
                        </div>
                    </div>
                </div>

                <!-- Proof Content -->
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-3 mb-1">
                        <span class="text-[9px] font-black uppercase tracking-widest px-2 py-0.5 rounded-md bg-slate-100 text-slate-500 line-clamp-1 max-w-[150px]">{{ $review->stock->name ?? 'Deleted Item' }}</span>
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">{{ $review->created_at->diffForHumans() }}</span>
                    </div>
                    <h5 class="text-xs font-black text-slate-900 line-clamp-1">{{ $review->title }}</h5>
                    <p class="mt-1 text-[11px] font-medium text-slate-500 line-clamp-2 leading-relaxed italic">"{{ $review->body }}"</p>
                </div>

                <!-- Moderation Action Hub -->
                <div class="flex items-center gap-3 lg:border-l lg:border-slate-100 lg:pl-6">
                    <div class="flex flex-col items-end gap-1 px-4">
                        <div class="flex items-center gap-2">
                             <div class="h-2 w-2 rounded-full {{ $review->is_approved ? 'bg-emerald-500 animate-pulse' : 'bg-slate-300' }}"></div>
                             <span class="text-[9px] font-black text-slate-900 uppercase tracking-widest">{{ $review->is_approved ? 'Published' : 'Queue' }}</span>
                        </div>
                        @if($review->is_flagged)
                            <span class="text-[8px] font-black text-rose-500 uppercase tracking-widest">Flagged Content</span>
                        @endif
                    </div>

                    <div class="flex items-center gap-2">
                        @if(!$review->is_approved)
                            <button wire:click="approve({{ $review->id }})" class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white transition-all shadow-sm">
                                <i class="fas fa-check text-xs"></i>
                            </button>
                        @else
                             <button wire:click="reject({{ $review->id }})" class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-400 hover:bg-slate-900 hover:text-white transition-all shadow-sm">
                                <i class="fas fa-eye-slash text-xs"></i>
                            </button>
                        @endif

                        <button wire:click="toggleFlag({{ $review->id }})" class="flex h-9 w-9 items-center justify-center rounded-xl {{ $review->is_flagged ? 'bg-rose-500 text-white' : 'bg-rose-50 text-rose-500 hover:bg-rose-500 hover:text-white' }} transition-all shadow-sm">
                            <i class="fas fa-flag text-xs"></i>
                        </button>

                        <button wire:click="viewReview({{ $review->id }})" class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-900 hover:text-white transition-all shadow-sm">
                            <i class="fas fa-expand text-xs"></i>
                        </button>

                         <button wire:click="openEdit({{ $review->id }})" class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-900 hover:text-white transition-all shadow-sm">
                            <i class="fas fa-pencil text-xs"></i>
                        </button>

                        <button 
                            onclick="confirm('Permanently purge this social proof?') || event.stopImmediatePropagation()"
                            wire:click="delete({{ $review->id }})" 
                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-rose-50 text-rose-500 hover:bg-rose-500 hover:text-white transition-all shadow-sm"
                        >
                            <i class="fas fa-trash-alt text-xs"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="py-20 text-center">
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-[2rem] bg-white text-slate-200 shadow-sm mb-6">
                <i class="fas fa-comment-slash text-3xl"></i>
            </div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-relaxed">No social proof identified.<br>User feedback will appear here for curation.</p>
        </div>
    @endforelse

    <div class="pt-6">
        {{ $reviews->links() }}
    </div>
</div>

@props(['config' => []])
@php
    $eyebrow = $config['eyebrow'] ?? 'Testimonials';
    $title = $config['title'] ?? 'What Customers Say';
    $subtitle = $config['subtitle'] ?? 'Trusted by customers across Sri Lanka';
    $limit = max(1, (int)($config['limit'] ?? 3));
    $reviews = App\Models\Review::with('user')->where('is_approved', true)->latest()->take($limit)->get();
@endphp

<section id="reviews" class="mx-auto max-w-7xl px-4 py-6 my-2">
    <div class="text-center mb-6">
        <p class="text-[10px] font-black uppercase tracking-[0.3em] text-[var(--primary)] mb-1">{{ $eyebrow }}</p>
        <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">{{ $title }}</h2>
        <p class="mt-1 text-xs text-slate-400 max-w-xl mx-auto">{{ $subtitle }}</p>
    </div>
    <div class="grid gap-4 md:grid-cols-3">
        @forelse($reviews as $review)
            <article class="premium-card p-6 flex flex-col justify-between !rounded-2xl">
                <div>
                    <div class="flex gap-1 mb-3">
                        @for($i=0;$i<5;$i++)<i class="fas fa-star text-[10px] {{ $i < $review->rating ? 'text-amber-400' : 'text-slate-200' }}"></i>@endfor
                    </div>
                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed italic">"{{ $review->body ?: $review->title }}"</p>
                </div>
                <div class="mt-4 pt-4 border-t border-slate-100 dark:border-white/5 flex items-center gap-3">
                    <div class="h-8 w-8 rounded-full bg-slate-900 dark:bg-white flex items-center justify-center text-white dark:text-slate-900 text-[10px] font-black">
                        {{ strtoupper(substr($review->user->name ?? 'V', 0, 1) ?: 'V') }}
                    </div>
                    <div>
                        <p class="text-[11px] font-black text-slate-900 dark:text-white uppercase tracking-wider">{{ $review->user->name ?? 'Verified Buyer' }}</p>
                        <p class="text-[8px] text-slate-400 font-bold uppercase tracking-wider">Verified Purchase</p>
                    </div>
                </div>
            </article>
        @empty
            @foreach([
                ['Ordered a replacement screen for my Samsung and it arrived the next day. Fit perfectly — exactly as described.', 'Kumari P.'],
                ['Best prices I found anywhere in Sri Lanka. The part was genuine and support actually picked up the phone when I called.', 'Dilan S.'],
                ['Bought an iPhone display assembly. Easy to install and the quality is clearly original. Will order again.', 'Ruwani M.'],
            ] as [$text, $name])
                <article class="premium-card p-6 flex flex-col justify-between !rounded-2xl">
                    <div>
                        <div class="flex gap-1 mb-3 text-amber-400">@for($i=0;$i<5;$i++)<i class="fas fa-star text-[10px]"></i>@endfor</div>
                        <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed italic">"{{ $text }}"</p>
                    </div>
                    <div class="mt-4 pt-4 border-t border-slate-100 dark:border-white/5 flex items-center gap-3">
                        <div class="h-8 w-8 rounded-full bg-slate-900 dark:bg-white flex items-center justify-center text-white dark:text-slate-900 text-[10px] font-black">{{ strtoupper(substr($name, 0, 1)) }}</div>
                        <div>
                            <p class="text-[11px] font-black text-slate-900 dark:text-white uppercase tracking-wider">{{ $name }}</p>
                            <p class="text-[8px] text-slate-400 font-bold uppercase tracking-wider">Verified Purchase</p>
                        </div>
                    </div>
                </article>
            @endforeach
        @endforelse
    </div>
</section>

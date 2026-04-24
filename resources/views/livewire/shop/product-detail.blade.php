<div>
    <div x-data="{ show:false, message:'', type:'success' }"
         x-on:notify.window="show=true;message=$event.detail.message;type=$event.detail.type;setTimeout(()=>show=false,3000)"
         x-show="show" x-transition
         class="fixed bottom-5 right-5 z-50 flex items-center gap-2 rounded-2xl px-5 py-3 text-sm font-semibold text-white shadow-xl"
         :class="type==='success'?'bg-green-500':(type==='error'?'bg-red-500':'bg-indigo-500')"
         style="display:none">
        <i class="fas fa-check-circle"></i><span x-text="message"></span>
    </div>

    <div class="mx-auto max-w-6xl py-8">
        <nav class="mb-8 flex items-center gap-2 text-sm text-slate-400">
            <a wire:navigate href="/" class="hover:text-slate-700 dark:hover:text-slate-100">Home</a>
            <i class="fas fa-chevron-right text-xs"></i>
            <a wire:navigate href="{{ url('/products') }}" class="hover:text-slate-700 dark:hover:text-slate-100">Products</a>
            <i class="fas fa-chevron-right text-xs"></i>
            <span class="max-w-xs truncate font-medium text-slate-700 dark:text-slate-100">{{ $product->name }}</span>
        </nav>

        <div class="grid grid-cols-1 gap-10 lg:grid-cols-2">
            <div>
                <div class="surface card-shadow storefront-reveal mb-3 flex h-96 items-center justify-center overflow-hidden rounded-[2rem]">
                    @if($imageUrls->isNotEmpty() && !empty($imageUrls[$activeImage]))
                        <picture class="block h-96 w-full">
                            @if(!empty($imageSourceSets[$activeImage]['webp']))
                                <source srcset="{{ $imageSourceSets[$activeImage]['webp'] }}" type="image/webp">
                            @endif
                            @if(!empty($imageSourceSets[$activeImage]['jpeg']))
                                <source srcset="{{ $imageSourceSets[$activeImage]['jpeg'] }}" type="image/jpeg">
                            @endif
                            <img src="{{ $imageSourceSets[$activeImage]['fallback'] ?? $imageUrls[$activeImage] }}" alt="{{ $product->name }}" loading="eager" decoding="async" class="h-96 w-full object-cover">
                        </picture>
                    @else
                        <i class="fas fa-box text-8xl text-slate-300"></i>
                    @endif
                </div>
                @if(!empty($product->images) && count($product->images) > 1)
                    <div class="flex gap-2 overflow-x-auto pb-1">
                        @foreach($product->images as $i => $img)
                            <button wire:click="setImage({{ $i }})" class="flex-shrink-0 overflow-hidden rounded-2xl border-2 transition"
                                    style="{{ $activeImage === $i ? 'border-color:var(--primary)' : 'border-color:transparent' }}">
                                <picture class="block h-16 w-16">
                                    @if(!empty($thumbnailSourceSets[$i]['webp']))
                                        <source srcset="{{ $thumbnailSourceSets[$i]['webp'] }}" type="image/webp">
                                    @endif
                                    @if(!empty($thumbnailSourceSets[$i]['jpeg']))
                                        <source srcset="{{ $thumbnailSourceSets[$i]['jpeg'] }}" type="image/jpeg">
                                    @endif
                                    <img src="{{ $thumbnailSourceSets[$i]['fallback'] ?? $thumbnailUrls[$i] ?? $imageUrls[$i] ?? '' }}" loading="lazy" decoding="async" class="h-16 w-16 object-cover">
                                </picture>
                            </button>
                        @endforeach
                    </div>
                @endif
                @if($videoUrls->isNotEmpty())
                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                        @foreach($videoUrls as $videoUrl)
                            <div class="surface overflow-hidden rounded-[1.5rem] border border-slate-200 dark:border-white/10">
                                <video controls preload="metadata" class="h-48 w-full bg-black object-cover">
                                    <source src="{{ $videoUrl }}">
                                </video>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div>
                <div class="glass card-shadow storefront-reveal storefront-reveal-delay-1 rounded-[2rem] p-6">
                    <div class="mb-3 flex flex-wrap items-center gap-2">
                        @if($product->brand)
                            <span class="rounded-full px-3 py-1 text-xs font-semibold" style="background:color-mix(in srgb,var(--primary) 12%,white);color:var(--primary)">{{ $product->brand->name }}</span>
                        @endif
                        @if($product->category)
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs text-slate-500 dark:bg-slate-800 dark:text-slate-300">{{ $product->category->name }}</span>
                        @endif
                        @if($product->qualityLevel)
                            <span class="rounded-full px-3 py-1 text-xs font-semibold" style="background:{{ $product->qualityLevel->color }}20;color:{{ $product->qualityLevel->color }}">{{ $product->qualityLevel->name }}</span>
                        @endif
                    </div>

                    <h1 class="mb-2 text-3xl font-black leading-tight text-adapt">{{ $product->name }}</h1>

                    <p class="mb-4 text-xs text-soft">
                        SKU: <span class="font-mono">{{ $product->sku }}</span>
                        @if($product->model_number) · <span class="font-mono">#{{ $product->model_number }}</span>@endif
                    </p>

                    <div class="mb-5 flex items-center gap-3">
                        @php $avg = $reviews->avg('rating') ?? 0; @endphp
                        <div class="flex gap-0.5">
                            @for($i=1;$i<=5;$i++)
                                <i class="fa{{ $i <= round($avg) ? 's' : 'r' }} fa-star text-sm text-yellow-400"></i>
                            @endfor
                        </div>
                        <span class="text-sm font-semibold text-adapt">{{ number_format($avg,1) }}</span>
                        <button wire:click="setTab('reviews')" class="text-sm hover:underline" style="color:var(--primary)">({{ $reviews->count() }} reviews)</button>
                    </div>

                    <div class="mb-6 flex items-end gap-4 rounded-[1.5rem] bg-white/70 p-4 dark:bg-slate-900/70">
                        @if($finalPrice !== null)
                            <span class="text-4xl font-extrabold text-adapt">Rs {{ number_format($finalPrice,2) }}</span>
                            <span class="text-xl text-soft line-through">Rs {{ number_format($product->selling_price,2) }}</span>
                            <span class="rounded-lg bg-green-100 px-2 py-1 text-sm font-bold text-green-600">
                                {{ $discount->type==='percentage' ? $discount->value.'% OFF' : 'Rs '.number_format($discount->value,2).' OFF' }}
                            </span>
                        @else
                            <span class="text-4xl font-extrabold text-adapt">Rs {{ number_format($product->selling_price,2) }}</span>
                        @endif
                    </div>

                    <div class="mb-6 grid gap-3 sm:grid-cols-3">
                        @foreach($detailSettings['trust_cards'] as $index => $trustCard)
                            <div class="rounded-2xl border border-slate-200 bg-white/75 p-4 dark:border-white/10 dark:bg-slate-900/60">
                                <div class="flex items-center gap-2 text-sm font-semibold text-adapt">
                                    <i class="fas {{ ['fa-bolt', 'fa-shield-check', 'fa-headset'][$index] ?? 'fa-star' }}" style="color:var(--primary)"></i>
                                    {{ $trustCard['title'] }}
                                </div>
                                <p class="mt-2 text-xs leading-6 text-soft">{{ $trustCard['text'] }}</p>
                            </div>
                        @endforeach
                    </div>

                    <div class="mb-5 grid grid-cols-2 gap-2 text-sm">
                        @foreach(array_filter(['Color'=>$product->color,'Size'=>$product->size,'Weight'=>$product->weight?$product->weight.'kg':null]) as $label=>$val)
                            <div class="rounded-xl border border-slate-200 bg-white/75 p-3 dark:border-white/10 dark:bg-slate-900/70">
                                <span class="text-xs text-soft">{{ $label }}:</span>
                                <span class="ml-1 font-medium text-adapt">{{ $val }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="mb-5 flex items-center gap-2">
                        @if($product->quantity > 0)
                            <span class="h-2.5 w-2.5 rounded-full bg-green-500 animate-pulse"></span>
                            <span class="text-sm font-medium text-green-600">{{ $detailSettings['in_stock_label'] }}{{ $product->isLowStock() ? ' - '.str_replace('{quantity}', $product->quantity, $detailSettings['low_stock_template']) : '' }}</span>
                        @else
                            <span class="h-2.5 w-2.5 rounded-full bg-red-500"></span>
                            <span class="text-sm font-medium text-red-600">{{ $detailSettings['out_of_stock_label'] }}</span>
                        @endif
                    </div>

                    <div class="mb-5 rounded-[1.5rem] border border-emerald-200 bg-emerald-50 p-4 dark:border-emerald-500/20 dark:bg-emerald-500/10">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm font-semibold text-emerald-800 dark:text-emerald-300">{{ $detailSettings['value_title'] }}</p>
                                <p class="mt-1 text-sm leading-6 text-emerald-700/90 dark:text-emerald-200/80">{{ $detailSettings['value_text'] }}</p>
                            </div>
                            <a wire:navigate href="{{ url('/checkout') }}" class="inline-flex items-center justify-center rounded-full bg-white px-4 py-2 text-xs font-bold text-emerald-700 shadow-sm dark:bg-slate-900 dark:text-emerald-300">
                                {{ $detailSettings['value_cta'] }}
                            </a>
                        </div>
                    </div>

                    <div class="mb-2 flex items-center gap-3">
                        <div class="flex items-center overflow-hidden rounded-xl border border-slate-200 dark:border-white/10">
                            <button wire:click="decrementQty" class="flex h-12 w-10 items-center justify-center text-lg font-bold text-slate-500 transition hover:bg-slate-100 dark:hover:bg-slate-800">-</button>
                            <span class="flex h-12 w-12 items-center justify-center text-sm font-semibold">{{ $quantity }}</span>
                            <button wire:click="incrementQty" class="flex h-12 w-10 items-center justify-center text-lg font-bold text-slate-500 transition hover:bg-slate-100 dark:hover:bg-slate-800">+</button>
                        </div>
                        <button wire:click="addToCart" wire:loading.attr="disabled" wire:target="addToCart" class="btn-gradient flex-1 rounded-2xl py-3 font-bold" {{ $product->quantity <= 0 ? 'disabled style=opacity:.5' : '' }}>
                            <span wire:loading.remove wire:target="addToCart"><i class="fas fa-shopping-cart mr-2"></i>Add to Cart</span>
                            <span wire:loading wire:target="addToCart"><i class="fas fa-spinner fa-spin mr-2"></i>Adding...</span>
                        </button>
                        <button wire:click="buyNow" wire:loading.attr="disabled" wire:target="buyNow" class="rounded-2xl border border-slate-200 px-5 py-3 font-bold text-slate-700 transition hover:bg-slate-50 dark:border-white/10 dark:text-slate-100 dark:hover:bg-slate-800" {{ $product->quantity <= 0 ? 'disabled style=opacity:.5' : '' }}>
                            <span wire:loading.remove wire:target="buyNow">Buy Now</span>
                            <span wire:loading wire:target="buyNow"><i class="fas fa-spinner fa-spin"></i></span>
                        </button>
                        <button wire:click="toggleWishlist" class="flex h-12 w-12 items-center justify-center rounded-2xl border transition" style="{{ $inWishlist ? 'border-color:#ef4444;color:#ef4444;background:#fef2f2' : 'border-color:#e5e7eb;color:#9ca3af' }}">
                            <i class="{{ $inWishlist ? 'fas' : 'far' }} fa-heart"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="surface card-shadow storefront-reveal storefront-reveal-delay-2 mt-12 overflow-hidden rounded-[2rem]">
            @php
                $detailTabs = [
                    'description' => 'Description',
                    'specs' => 'Specifications',
                ];

                if ($detailSettings['show_reviews']) {
                    $detailTabs['reviews'] = 'Reviews ('.$reviews->count().')';
                }
            @endphp
            <div class="flex border-b border-slate-200 px-2 dark:border-white/10">
                @foreach($detailTabs as $tab=>$label)
                    <button wire:click="setTab('{{ $tab }}')" class="border-b-2 px-5 py-4 text-sm font-semibold transition"
                            style="{{ $activeTab===$tab ? 'border-color:var(--primary);color:var(--primary)' : 'border-color:transparent;color:#6b7280' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            @if($activeTab === 'description')
                <div class="p-8">
                    <div class="leading-relaxed text-soft">{!! nl2br(e($product->description ?? 'No description available.')) !!}</div>
                    @if($product->notes)
                        <div class="mt-5 rounded-xl border border-blue-100 bg-blue-50 p-4">
                            <p class="text-sm text-blue-700"><i class="fas fa-info-circle mr-2"></i>{{ $product->notes }}</p>
                        </div>
                    @endif
                </div>
            @endif

            @if($activeTab === 'specs')
                <div class="p-8">
                    <div class="grid grid-cols-1 gap-0 md:grid-cols-2">
                        @foreach(array_filter(['SKU'=>$product->sku,'Item Code'=>$product->item_code,'Brand'=>$product->brand?->name,'Make'=>$product->make?->name,'Model'=>$product->model_name,'Model #'=>$product->model_number,'Color'=>$product->color,'Size'=>$product->size,'Weight'=>$product->weight?$product->weight.'kg':null,'Category'=>$product->category?->name,'Item Type'=>$product->itemType?->name,'Warranty'=>$product->warranty?->name,'Barcode'=>$product->barcode]) as $label=>$val)
                            <div class="flex justify-between border-b border-slate-100 py-3 dark:border-white/5">
                                <span class="text-sm text-soft">{{ $label }}</span>
                                <span class="text-sm font-semibold text-adapt">{{ $val }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($activeTab === 'reviews' && $detailSettings['show_reviews'])
                <div class="p-8">
                    <div class="mb-8 flex flex-col gap-8 md:flex-row">
                        <div class="text-center md:w-40">
                            <div class="text-6xl font-black text-adapt">{{ number_format($reviews->avg('rating') ?? 0,1) }}</div>
                            <div class="my-2 flex justify-center gap-0.5">
                                @for($i=1;$i<=5;$i++)
                                    <i class="fa{{ $i <= round($reviews->avg('rating')??0) ? 's' : 'r' }} fa-star text-yellow-400"></i>
                                @endfor
                            </div>
                            <p class="text-xs text-soft">{{ $reviews->count() }} reviews</p>
                        </div>
                        <div class="flex-1 space-y-1.5">
                            @for($star=5;$star>=1;$star--)
                                @php $cnt=$reviews->where('rating',$star)->count(); $pct=$reviews->count()?round($cnt/$reviews->count()*100):0; @endphp
                                <div class="flex items-center gap-2 text-xs">
                                    <span class="w-3">{{ $star }}</span>
                                    <i class="fas fa-star text-yellow-400"></i>
                                    <div class="h-2 flex-1 overflow-hidden rounded-full bg-slate-100 dark:bg-slate-800">
                                        <div class="h-full rounded-full bg-yellow-400" style="width:{{ $pct }}%"></div>
                                    </div>
                                    <span class="w-5 text-soft">{{ $cnt }}</span>
                                </div>
                            @endfor
                        </div>
                    </div>

                    @forelse($reviews as $review)
                        <div class="mb-5 border-b border-slate-100 pb-5 last:border-0 dark:border-white/5">
                            <div class="flex items-start gap-3">
                                <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full text-sm font-bold text-white" style="background:var(--primary)">
                                    {{ strtoupper(substr($review->user->name ?? 'U', 0, 1)) }}
                                </div>
                                <div class="flex-1">
                                    <div class="mb-1 flex items-center justify-between">
                                        <span class="text-sm font-semibold text-adapt">{{ $review->user->name ?? 'Anonymous' }}</span>
                                        <span class="text-xs text-soft">{{ $review->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="mb-1.5 flex gap-0.5">
                                        @for($i=1;$i<=5;$i++)<i class="fa{{ $i<=$review->rating?'s':'r' }} fa-star text-xs text-yellow-400"></i>@endfor
                                    </div>
                                    @if($review->title)<p class="mb-1 text-sm font-medium text-adapt">{{ $review->title }}</p>@endif
                                    <p class="text-sm leading-relaxed text-soft">{{ $review->body }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="py-10 text-center text-soft">
                            <i class="fas fa-comment-slash mb-2 block text-3xl opacity-30"></i>
                            <p class="font-medium">No reviews yet. Be the first!</p>
                        </div>
                    @endforelse

                    @auth
                        <div class="mt-6 rounded-2xl bg-slate-50 p-6 dark:bg-slate-900/60">
                            <h4 class="mb-4 font-bold text-adapt">Write a Review</h4>
                            <div class="mb-4">
                                <label class="mb-2 block text-sm font-semibold text-adapt">Rating *</label>
                                <div class="flex gap-2">
                                    @for($i=1;$i<=5;$i++)
                                        <button wire:click="$set('rating', {{ $i }})" class="text-2xl transition hover:scale-110" style="color: {{ $rating >= $i ? '#f59e0b' : '#d1d5db' }}">
                                            <i class="fas fa-star"></i>
                                        </button>
                                    @endfor
                                </div>
                            </div>
                            <div class="mb-3">
                                <input type="text" wire:model="reviewTitle" placeholder="Review title (optional)" class="field">
                                @error('reviewTitle')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                            </div>
                            <div class="mb-4">
                                <textarea wire:model="reviewBody" rows="3" placeholder="Share your experience..." required class="field resize-none"></textarea>
                                @error('reviewBody')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                            </div>
                            <button wire:click="submitReview" wire:loading.attr="disabled" class="btn-gradient rounded-xl px-6 py-2.5 text-sm font-semibold">
                                <span wire:loading.remove>Submit Review</span>
                                <span wire:loading><i class="fas fa-spinner fa-spin mr-1"></i>Submitting...</span>
                            </button>
                        </div>
                    @else
                        <div class="mt-5 rounded-2xl bg-slate-50 p-5 text-center dark:bg-slate-900/60">
                            <p class="mb-3 text-sm text-soft">Log in to write a review</p>
                            <a wire:navigate href="{{ route('login') }}" class="btn-gradient inline-block rounded-xl px-5 py-2 text-sm font-semibold">Log In</a>
                        </div>
                    @endauth
                </div>
            @endif
        </div>

        @if($detailSettings['show_related'] && $related->isNotEmpty())
            <div class="storefront-reveal storefront-reveal-delay-3 mt-12">
                <h2 class="mb-5 text-2xl font-bold text-adapt">{{ $detailSettings['related_title'] }}</h2>
                <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                    @foreach($related as $rp)
                        <a wire:navigate href="{{ url('/products/'.$rp->id) }}" class="surface card-shadow group overflow-hidden rounded-[1.5rem] transition-all hover:-translate-y-1">
                            <div class="flex h-36 items-center justify-center overflow-hidden bg-gradient-to-br from-white to-violet-50 dark:from-slate-900 dark:to-slate-800">
                                @if($rp->primary_image_url)
                                    <picture class="block h-36 w-full">
                                        @if(!empty($rp->primary_image_sources['webp']))
                                            <source srcset="{{ $rp->primary_image_sources['webp'] }}" type="image/webp">
                                        @endif
                                        @if(!empty($rp->primary_image_sources['jpeg']))
                                            <source srcset="{{ $rp->primary_image_sources['jpeg'] }}" type="image/jpeg">
                                        @endif
                                        <img src="{{ $rp->primary_image_sources['fallback'] ?? $rp->primary_image_url }}" loading="lazy" decoding="async" class="h-36 w-full object-cover transition group-hover:scale-105">
                                    </picture>
                                @else
                                    <i class="fas fa-box text-3xl text-slate-300"></i>
                                @endif
                            </div>
                            <div class="p-3">
                                <p class="truncate text-xs text-soft">{{ $rp->brand?->name ?? '' }}</p>
                                <p class="line-clamp-2 text-sm font-semibold text-adapt">{{ $rp->name }}</p>
                                <p class="mt-1 text-sm font-bold text-adapt">Rs {{ number_format($rp->selling_price,2) }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

@php
    $primaryColor = $primaryColor ?? '#4f46e5';
    $badge        = $badge ?? null;
    $discount     = \App\Models\Discount::active()
        ->where(function($q) use ($product) {
            $q->where('scope','all')
              ->orWhere(fn($q2) => $q2->where('scope','product')->where('scope_id',$product->id))
              ->orWhere(fn($q2) => $q2->where('scope','category')->where('scope_id',$product->category_id));
        })
        ->orderByDesc('value')
        ->first();
    $finalPrice = $discount
        ? max(0, $product->selling_price - $discount->calculateDiscount($product->selling_price))
        : null;
    $inWishlist = in_array($product->id, session('wishlist', []));
@endphp

<div class="group bg-white border border-gray-100 rounded-xl overflow-hidden hover:shadow-lg transition-all duration-300">

    {{-- Image → product detail --}}
    <a href="{{ url('/products/'.$product->id) }}" class="block relative overflow-hidden">
        <div class="w-full h-56 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center
                    group-hover:from-indigo-50 group-hover:to-indigo-100 transition-all duration-300">
            @if(!empty($product->images) && count($product->images))
                <img src="{{ Storage::url($product->images[0]) }}" alt="{{ $product->name }}"
                     class="w-full h-56 object-cover group-hover:scale-105 transition duration-300">
            @else
                <i class="fas fa-box text-5xl text-gray-300"></i>
            @endif
        </div>

        {{-- Badges --}}
        <div class="absolute top-3 left-3 flex flex-col gap-1">
            @if($badge === 'New')
                <span class="bg-blue-500 text-white text-xs px-2 py-1 rounded font-semibold">NEW</span>
            @endif
            @if($badge === 'Deal')
                <span class="bg-orange-500 text-white text-xs px-2 py-1 rounded font-semibold">DEAL</span>
            @endif
            @if($discount)
                <span class="bg-green-500 text-white text-xs px-2 py-1 rounded font-semibold">
                    {{ $discount->type === 'percentage'
                        ? '-'.$discount->value.'%'
                        : '-Rs '.number_format($discount->value, 0) }}
                </span>
            @endif
        </div>
    </a>

    {{-- Wishlist button — floats over the bottom of the image --}}
    <div class="relative -mt-5 pr-3 flex justify-end">
        <button type="button"
                class="shop-wishlist-btn w-9 h-9 bg-white rounded-full shadow-md flex items-center justify-center
                       hover:scale-110 transition z-10 relative"
                data-id="{{ $product->id }}">
            <i class="{{ $inWishlist ? 'fas fa-heart text-red-500' : 'far fa-heart text-gray-400' }} text-sm"></i>
        </button>
    </div>

    {{-- Text --}}
    <div class="p-4 pt-1">
        <a href="{{ url('/products/'.$product->id) }}" class="block">
            <div class="text-xs text-gray-400 mb-1">
                {{ $product->brand->name ?? ($product->category->name ?? '') }}
            </div>
            <h3 class="font-semibold text-gray-900 mb-1 truncate hover:opacity-75 transition">
                {{ $product->name }}
            </h3>
            @if($product->model_name || $product->model_number)
                <p class="text-xs text-gray-400 mb-2">
                    {{ implode(' · ', array_filter([$product->model_name, $product->model_number])) }}
                </p>
            @endif
        </a>

        <div class="flex items-center mb-3">
            <div class="flex text-yellow-400 text-xs">
                @for($i = 0; $i < 5; $i++)<i class="fas fa-star"></i>@endfor
            </div>
            <span class="text-gray-400 text-xs ml-1">(0)</span>
        </div>

        <div class="flex items-center justify-between">
            <div>
                @if($finalPrice !== null)
                    <span class="text-lg font-bold text-gray-900">Rs {{ number_format($finalPrice, 2) }}</span>
                    <span class="text-sm text-gray-400 line-through ml-1">Rs {{ number_format($product->selling_price, 2) }}</span>
                @else
                    <span class="text-lg font-bold text-gray-900">Rs {{ number_format($product->selling_price, 2) }}</span>
                @endif
            </div>

            <button type="button"
                    class="shop-cart-btn text-white p-2 rounded-lg hover:opacity-90 transition"
                    style="background:{{ $primaryColor }}"
                    data-id="{{ $product->id }}">
                <i class="fas fa-shopping-cart text-sm"></i>
            </button>
        </div>

        @if($product->isLowStock())
            <p class="text-xs text-orange-500 mt-2">
                <i class="fas fa-exclamation-triangle mr-1"></i>Only {{ $product->quantity }} left!
            </p>
        @endif
    </div>
</div>
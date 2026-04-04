<div class="max-w-6xl mx-auto">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Display Items</h1>
            <p class="text-gray-500 text-sm mt-1">Choose which products appear in Featured, New Arrivals, and Deals sections.</p>
        </div>
        <button wire:click="save"
                class="bg-orange-600 hover:bg-orange-700 text-white font-semibold px-6 py-2.5 rounded-lg shadow flex items-center gap-2">
            <span wire:loading.remove wire:target="save"><i class="fas fa-save"></i> Save Selections</span>
            <span wire:loading wire:target="save"><i class="fas fa-spinner fa-spin"></i> Saving...</span>
        </button>
    </div>

    <!-- Section Title Editors -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-indigo-200 p-4">
            <label class="block text-xs font-semibold text-indigo-600 uppercase mb-1">Featured Section Title</label>
            <input type="text" wire:model="featuredSectionTitle"
                   class="w-full border-gray-300 rounded-lg text-sm">
        </div>
        <div class="bg-white rounded-xl border border-purple-200 p-4">
            <label class="block text-xs font-semibold text-purple-600 uppercase mb-1">New Arrivals Title</label>
            <input type="text" wire:model="newArrivalsSectionTitle"
                   class="w-full border-gray-300 rounded-lg text-sm">
        </div>
        <div class="bg-white rounded-xl border border-orange-200 p-4">
            <label class="block text-xs font-semibold text-orange-600 uppercase mb-1">Deals Section Title</label>
            <input type="text" wire:model="dealsSectionTitle"
                   class="w-full border-gray-300 rounded-lg text-sm">
        </div>
    </div>

    <!-- Counts Summary -->
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-indigo-50 rounded-xl p-3 text-center">
            <p class="text-2xl font-bold text-indigo-600">{{ count($featuredIds) }}</p>
            <p class="text-xs text-indigo-500">Featured</p>
        </div>
        <div class="bg-purple-50 rounded-xl p-3 text-center">
            <p class="text-2xl font-bold text-purple-600">{{ count($newArrivalsIds) }}</p>
            <p class="text-xs text-purple-500">New Arrivals</p>
        </div>
        <div class="bg-orange-50 rounded-xl p-3 text-center">
            <p class="text-2xl font-bold text-orange-600">{{ count($dealIds) }}</p>
            <p class="text-xs text-orange-500">Deals</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="flex gap-3 mb-4">
        <input type="text" wire:model.live.debounce.300ms="search"
               placeholder="Search products..."
               class="flex-1 md:max-w-xs px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 text-sm">
        <select wire:model.live="selectedCategory" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
            <option value="">All Categories</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
            @endforeach
        </select>
    </div>

    <!-- Products Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @forelse($stocks as $stock)
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition">
            <!-- Product Info -->
            <div class="p-4">
                <div class="font-semibold text-gray-900 text-sm mb-0.5">{{ $stock->name }}</div>
                <div class="text-xs text-gray-400 mb-1">{{ $stock->sku }} · {{ $stock->category->name ?? 'N/A' }}</div>
                <div class="text-sm font-bold text-gray-800">Rs {{ number_format($stock->selling_price, 2) }}</div>
            </div>

            <!-- Toggle Buttons -->
            <div class="px-4 pb-4 grid grid-cols-3 gap-1">
                <!-- Featured -->
                <button wire:click="toggleFeatured({{ $stock->id }})"
                        class="py-1.5 rounded-lg text-xs font-semibold transition border
                               {{ in_array($stock->id, $featuredIds) ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-indigo-600 border-indigo-300 hover:bg-indigo-50' }}">
                    <i class="fas fa-star text-xs mb-0.5 block mx-auto"></i>
                    Featured
                </button>

                <!-- New Arrival -->
                <button wire:click="toggleNewArrival({{ $stock->id }})"
                        class="py-1.5 rounded-lg text-xs font-semibold transition border
                               {{ in_array($stock->id, $newArrivalsIds) ? 'bg-purple-600 text-white border-purple-600' : 'bg-white text-purple-600 border-purple-300 hover:bg-purple-50' }}">
                    <i class="fas fa-sparkles text-xs mb-0.5 block mx-auto"></i>
                    New
                </button>

                <!-- Deal -->
                <button wire:click="toggleDeal({{ $stock->id }})"
                        class="py-1.5 rounded-lg text-xs font-semibold transition border
                               {{ in_array($stock->id, $dealIds) ? 'bg-orange-600 text-white border-orange-600' : 'bg-white text-orange-600 border-orange-300 hover:bg-orange-50' }}">
                    <i class="fas fa-tag text-xs mb-0.5 block mx-auto"></i>
                    Deal
                </button>
            </div>
        </div>
        @empty
        <div class="col-span-4 py-16 text-center text-gray-400">
            <i class="fas fa-box-open text-4xl mb-3 block"></i>
            No active products found.
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-6">{{ $stocks->links() }}</div>
</div>
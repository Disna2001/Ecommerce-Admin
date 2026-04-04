<div class="max-w-6xl mx-auto">

    @if(session('message'))
    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('message') }}</div>
    @endif

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Discount Management</h1>
            <p class="text-gray-500 text-sm mt-1">Create percentage/fixed discounts with optional timers and coupon codes.</p>
        </div>
        <button wire:click="openModal"
                class="bg-green-600 hover:bg-green-700 text-white font-semibold px-5 py-2.5 rounded-lg shadow flex items-center gap-2">
            <i class="fas fa-plus"></i> Add Discount
        </button>
    </div>

    <!-- Search -->
    <div class="mb-4">
        <input type="text" wire:model.live.debounce.300ms="search"
               placeholder="Search by name or code..."
               class="w-full md:w-96 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Name</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Code</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Value</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Scope</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Timer</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Usage</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($discounts as $discount)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-900">{{ $discount->name }}</div>
                        @if($discount->description)
                        <div class="text-xs text-gray-400">{{ Str::limit($discount->description, 40) }}</div>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        @if($discount->code)
                        <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded font-mono text-xs">{{ $discount->code }}</span>
                        @else
                        <span class="text-gray-400 text-xs">Auto-apply</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <span class="font-bold text-green-700">
                            @if($discount->type === 'percentage')
                                {{ $discount->value }}%
                            @else
                                Rs {{ number_format($discount->value, 2) }}
                            @endif
                        </span>
                        @if($discount->max_discount_amount)
                        <div class="text-xs text-gray-400">Max: Rs {{ number_format($discount->max_discount_amount, 2) }}</div>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600">
                        {{ ucfirst($discount->scope) }}
                    </td>
                    <td class="px-4 py-3 text-xs">
                        @if($discount->has_timer && $discount->ends_at)
                            @if($discount->isExpired())
                                <span class="text-red-500 font-medium"><i class="fas fa-clock mr-1"></i>Expired</span>
                            @else
                                <span class="text-orange-600 font-medium">
                                    <i class="fas fa-hourglass-half mr-1"></i>
                                    Ends {{ $discount->ends_at->diffForHumans() }}
                                </span>
                                @if($discount->show_timer_on_site)
                                <div class="text-gray-400">Shown on site</div>
                                @endif
                            @endif
                        @else
                            <span class="text-gray-400">No timer</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600">
                        {{ $discount->used_count }}{{ $discount->usage_limit ? ' / ' . $discount->usage_limit : ' / ∞' }}
                    </td>
                    <td class="px-4 py-3">
                        <button wire:click="toggleActive({{ $discount->id }})"
                                class="px-2 py-1 rounded text-xs font-semibold {{ $discount->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $discount->is_active ? 'Active' : 'Inactive' }}
                        </button>
                    </td>
                    <td class="px-4 py-3 flex items-center gap-2">
                        <button wire:click="edit({{ $discount->id }})" class="text-indigo-600 hover:text-indigo-800 text-sm">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button wire:click="delete({{ $discount->id }})"
                                onclick="confirm('Delete this discount?') || event.stopImmediatePropagation()"
                                class="text-red-500 hover:text-red-700 text-sm">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-12 text-center text-gray-400">
                        <i class="fas fa-tag text-4xl mb-3 block"></i>
                        No discounts yet. Click "Add Discount" to create one.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">{{ $discounts->links() }}</div>
    </div>

    <!-- Modal -->
    @if($isOpen)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black opacity-50"></div>
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl z-50 relative">
                <div class="px-6 py-4 border-b flex justify-between items-center">
                    <h3 class="text-lg font-bold">{{ $discount_id ? 'Edit Discount' : 'New Discount' }}</h3>
                    <button wire:click="$set('isOpen', false)" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <form wire:submit.prevent="store">
                    <div class="p-6 max-h-[75vh] overflow-y-auto grid grid-cols-1 md:grid-cols-2 gap-4">

                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Discount Name *</label>
                            <input type="text" wire:model="name"
                                   class="w-full border-gray-300 rounded-lg shadow-sm"
                                   placeholder="Summer Sale 20%">
                            @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Coupon Code -->
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Coupon Code (optional)</label>
                            <div class="flex gap-2">
                                <input type="text" wire:model="code"
                                       class="flex-1 border-gray-300 rounded-lg shadow-sm font-mono uppercase"
                                       placeholder="Leave blank to auto-apply">
                                <button type="button" wire:click="generateCode"
                                        class="px-3 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm hover:bg-gray-200">
                                    <i class="fas fa-random mr-1"></i> Generate
                                </button>
                            </div>
                            @error('code') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Type & Value -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                            <select wire:model.live="type" class="w-full border-gray-300 rounded-lg shadow-sm">
                                <option value="percentage">Percentage (%)</option>
                                <option value="fixed">Fixed Amount (Rs)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Value * {{ $type === 'percentage' ? '(%)' : '(Rs)' }}
                            </label>
                            <input type="number" step="0.01" min="0" wire:model="value"
                                   class="w-full border-gray-300 rounded-lg shadow-sm"
                                   placeholder="{{ $type === 'percentage' ? 'e.g., 20' : 'e.g., 500' }}">
                            @error('value') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Min Order Amount (Rs)</label>
                            <input type="number" step="0.01" min="0" wire:model="min_order_amount"
                                   class="w-full border-gray-300 rounded-lg shadow-sm" placeholder="0 = no minimum">
                        </div>

                        @if($type === 'percentage')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Max Discount Cap (Rs)</label>
                            <input type="number" step="0.01" min="0" wire:model="max_discount_amount"
                                   class="w-full border-gray-300 rounded-lg shadow-sm" placeholder="Leave blank = no cap">
                        </div>
                        @endif

                        <!-- Scope -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Applies To</label>
                            <select wire:model.live="scope" class="w-full border-gray-300 rounded-lg shadow-sm">
                                <option value="all">All Products</option>
                                <option value="category">Specific Category</option>
                                <option value="product">Specific Product</option>
                            </select>
                        </div>

                        @if($scope === 'category')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                            <select wire:model="scope_id" class="w-full border-gray-300 rounded-lg shadow-sm">
                                <option value="">Select Category</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        @if($scope === 'product')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Product</label>
                            <select wire:model="scope_id" class="w-full border-gray-300 rounded-lg shadow-sm">
                                <option value="">Select Product</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->sku }})</option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        <!-- Timer Section -->
                        <div class="col-span-2 border-t pt-4 mt-2">
                            <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-3">
                                <input type="checkbox" wire:model.live="has_timer" class="rounded">
                                Enable Countdown Timer
                            </label>

                            @if($has_timer)
                            <div class="grid grid-cols-2 gap-4 pl-2 border-l-4 border-orange-300">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Starts At</label>
                                    <input type="datetime-local" wire:model="starts_at" class="w-full border-gray-300 rounded-lg text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Ends At *</label>
                                    <input type="datetime-local" wire:model="ends_at" class="w-full border-gray-300 rounded-lg text-sm">
                                    @error('ends_at') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Timer Label</label>
                                    <input type="text" wire:model="timer_label" class="w-full border-gray-300 rounded-lg text-sm"
                                           placeholder="Sale ends in:">
                                </div>
                                <div class="flex items-center">
                                    <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
                                        <input type="checkbox" wire:model="show_timer_on_site" class="rounded">
                                        Show countdown on site
                                    </label>
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Usage Limit -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Usage Limit</label>
                            <input type="number" wire:model="usage_limit" min="1"
                                   class="w-full border-gray-300 rounded-lg shadow-sm"
                                   placeholder="Leave blank = unlimited">
                        </div>

                        <!-- Active -->
                        <div class="flex items-center mt-5">
                            <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
                                <input type="checkbox" wire:model="is_active" class="rounded"> Active
                            </label>
                        </div>

                        <!-- Description -->
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Internal Notes</label>
                            <textarea wire:model="description" rows="2"
                                      class="w-full border-gray-300 rounded-lg shadow-sm text-sm"
                                      placeholder="For internal reference only..."></textarea>
                        </div>
                    </div>

                    <div class="px-6 py-4 border-t flex justify-end gap-2">
                        <button type="button" wire:click="$set('isOpen', false)"
                                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">Cancel</button>
                        <button type="submit"
                                class="px-5 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">
                            {{ $discount_id ? 'Update Discount' : 'Create Discount' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
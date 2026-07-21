<div class="space-y-6">
    @if (session()->has('message'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs font-semibold text-emerald-700">{{ session('message') }}</div>
    @endif

    @if (session()->has('error'))
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-xs font-semibold text-rose-700">{{ session('error') }}</div>
    @endif

    <!-- Content Header & Actions -->
    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-xs">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Catalog Setup</p>
                <h2 class="mt-1 text-xl font-bold text-slate-900">Category Management</h2>
                <p class="mt-1 text-xs text-slate-500">Organize products into category groups for intuitive storefront navigation and filtering.</p>
            </div>

            <button wire:click="openModal" class="inline-flex items-center gap-2 rounded-lg bg-slate-900 px-4 py-2 text-xs font-semibold text-white transition hover:bg-slate-800 shadow-xs">
                <i class="fas fa-plus text-xs"></i>
                <span>New Category</span>
            </button>
        </div>

        <!-- Consistent Stat Cards KPI Row -->
        <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <x-admin.dashboard.stat-card label="Total Categories" :value="$totalCategories" icon="fa-tags" tone="indigo" />
            <x-admin.dashboard.stat-card label="Active" :value="$activeCategories" icon="fa-circle-check" tone="emerald" />
            <x-admin.dashboard.stat-card label="With Products" :value="$categoriesWithProducts" icon="fa-boxes-stacked" tone="slate" />
            <x-admin.dashboard.stat-card label="Empty Groups" :value="$emptyCategories" icon="fa-folder-open" tone="amber" />
        </div>
    </div>

    <!-- Search & Guidance Grid -->
    <div class="grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-xs">
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-400">Search Categories</label>
            <div class="relative mt-2">
                <i class="fas fa-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by category name or description..." class="w-full rounded-lg border-slate-200 pl-9 text-xs font-semibold shadow-xs focus:border-slate-900 focus:ring-0">
            </div>
        </div>

        <x-admin.catalog.guidance-panel 
            title="Category Guidance" 
            tip="Keep category names clear and concise. Avoid overlapping or duplicate categories so customer search and catalog filtering stay intuitive." 
            icon="fa-layer-group" 
        />
    </div>

    <!-- Table -->
    <div class="rounded-xl border border-slate-200 bg-white shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50 text-left text-[10px] font-bold uppercase tracking-wider text-slate-400">
                    <tr>
                        <th class="px-6 py-3.5">Category</th>
                        <th class="px-6 py-3.5">Slug</th>
                        <th class="px-6 py-3.5">Description</th>
                        <th class="px-6 py-3.5">Products</th>
                        <th class="px-6 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white text-xs">
                    @forelse($categories as $category)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-900">{{ $category->name }}</p>
                                <p class="text-[10px] font-medium text-slate-400">ID #{{ $category->id }}</p>
                            </td>
                            <td class="px-6 py-4 font-mono text-[11px] text-slate-500">{{ $category->slug }}</td>
                            <td class="px-6 py-4 text-slate-500">{{ \Illuminate\Support\Str::limit($category->description, 80) ?: 'No description added.' }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex rounded-md bg-indigo-50 px-2.5 py-1 text-xs font-bold text-indigo-700 border border-indigo-100">{{ $category->stocks_count }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button wire:click="edit({{ $category->id }})" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-50 hover:text-slate-900 shadow-xs">
                                        <i class="fas fa-pen text-[10px] text-slate-400"></i> Edit
                                    </button>
                                    <button wire:click="delete({{ $category->id }})" onclick="confirm('Delete this category?') || event.stopImmediatePropagation()" class="inline-flex items-center gap-1.5 rounded-lg border border-rose-200 bg-rose-50 px-2.5 py-1.5 text-xs font-semibold text-rose-600 transition hover:bg-rose-100 shadow-xs">
                                        <i class="fas fa-trash text-[10px]"></i> Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-400 font-medium">No categories found matching your search.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($categories->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $categories->links() }}
            </div>
        @endif
    </div>

    <!-- Category Modal -->
    @if($isOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
            <div class="w-full max-w-lg rounded-xl border border-slate-200 bg-white p-6 shadow-xl space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <h3 class="text-base font-bold text-slate-900">{{ $category_id ? 'Edit Category' : 'New Category' }}</h3>
                    <button wire:click="closeModal" class="text-slate-400 hover:text-slate-600"><i class="fas fa-times"></i></button>
                </div>

                <form wire:submit.prevent="store" class="space-y-4 text-xs">
                    <div class="space-y-1">
                        <label class="block font-bold text-slate-700">Category Name</label>
                        <input type="text" wire:model="name" class="w-full rounded-lg border-slate-200 px-3 py-2 font-semibold text-slate-900 focus:ring-0">
                        @error('name') <span class="text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="block font-bold text-slate-700">Description</label>
                        <textarea wire:model="description" rows="3" class="w-full rounded-lg border-slate-200 px-3 py-2 font-medium text-slate-900 focus:ring-0"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button" wire:click="closeModal" class="rounded-lg border border-slate-200 bg-white px-4 py-2 font-semibold text-slate-700 hover:bg-slate-50">Cancel</button>
                        <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 font-semibold text-white hover:bg-slate-800 shadow-xs">{{ $category_id ? 'Update Category' : 'Create Category' }}</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

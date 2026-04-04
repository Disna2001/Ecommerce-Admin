<div class="space-y-6">
    @if (session()->has('message'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">{{ session('message') }}</div>
    @endif

    @if (session()->has('error'))
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700">{{ session('error') }}</div>
    @endif

    <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Catalog Setup</p>
                <h2 class="mt-2 text-2xl font-bold text-slate-900">Category Management</h2>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">Organize products into clearer category groups so storefront filtering and inventory management stay clean.</p>
            </div>

            <button wire:click="openModal" class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                <i class="fas fa-plus"></i>
                <span>New Category</span>
            </button>
        </div>

        <div class="mt-6 grid gap-4 md:grid-cols-4">
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Total Categories</p>
                <p class="mt-2 text-3xl font-black text-slate-900">{{ $totalCategories }}</p>
            </div>
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-emerald-500">Active</p>
                <p class="mt-2 text-3xl font-black text-emerald-700">{{ $activeCategories }}</p>
            </div>
            <div class="rounded-2xl border border-indigo-200 bg-indigo-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-indigo-500">With Products</p>
                <p class="mt-2 text-3xl font-black text-indigo-700">{{ $categoriesWithProducts }}</p>
            </div>
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-amber-500">Empty Groups</p>
                <p class="mt-2 text-3xl font-black text-amber-700">{{ $emptyCategories }}</p>
            </div>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
        <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
            <label class="block text-sm font-medium text-slate-700">Search Categories</label>
            <div class="relative mt-2">
                <i class="fas fa-search pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by category name or description..." class="w-full rounded-2xl border-slate-200 pl-11 text-sm shadow-none focus:ring-0">
            </div>
        </div>

        <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-start gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600">
                    <i class="fas fa-layer-group"></i>
                </div>
                <div>
                    <h3 class="text-xl font-semibold text-slate-900">Category Guidance</h3>
                    <p class="mt-1 text-sm text-slate-500">Keep names short, descriptions useful, and avoid duplicate groups that confuse storefront filtering.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="rounded-[1.75rem] border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="border-b border-slate-200 bg-slate-50 text-left text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">
                    <tr>
                        <th class="px-6 py-4">Category</th>
                        <th class="px-6 py-4">Slug</th>
                        <th class="px-6 py-4">Description</th>
                        <th class="px-6 py-4">Products</th>
                        <th class="px-6 py-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($categories as $category)
                        <tr>
                            <td class="px-6 py-4">
                                <p class="text-sm font-semibold text-slate-900">{{ $category->name }}</p>
                                <p class="mt-1 text-xs text-slate-400">ID #{{ $category->id }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-500">{{ $category->slug }}</td>
                            <td class="px-6 py-4 text-sm text-slate-500">{{ \Illuminate\Support\Str::limit($category->description, 80) ?: 'No description added.' }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-700">{{ $category->stocks_count }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-2">
                                    <button wire:click="edit({{ $category->id }})" class="inline-flex items-center gap-2 rounded-full border border-indigo-200 bg-indigo-50 px-3 py-2 text-xs font-semibold text-indigo-700 transition hover:bg-indigo-100">
                                        <i class="fas fa-pen"></i> Edit
                                    </button>
                                    <button wire:click="delete({{ $category->id }})" onclick="confirm('Delete this category?') || event.stopImmediatePropagation()" class="inline-flex items-center gap-2 rounded-full border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700 transition hover:bg-rose-100">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-16 text-center text-sm text-slate-500">No categories found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>{{ $categories->links() }}</div>

    @if($isOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex min-h-screen items-start justify-center px-4 py-8">
                <div class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm" wire:click="closeModal"></div>
                <div class="relative z-10 w-full max-w-2xl overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-2xl">
                    <form wire:submit="store">
                        <div class="border-b border-slate-200 bg-slate-50 px-6 py-5">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Category Window</p>
                                    <h3 class="mt-2 text-2xl font-bold text-slate-900">{{ $category_id ? 'Edit Category' : 'Add New Category' }}</h3>
                                </div>
                                <button type="button" wire:click="closeModal" class="rounded-full border border-slate-200 p-3 text-slate-500 transition hover:bg-white hover:text-slate-700"><i class="fas fa-xmark"></i></button>
                            </div>
                        </div>
                        <div class="space-y-5 px-6 py-6">
                            <div>
                                <label class="block text-sm font-medium text-slate-700">Category Name</label>
                                <input type="text" wire:model="name" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none">
                                @error('name') <span class="mt-1 block text-xs text-rose-500">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700">Description</label>
                                <textarea wire:model="description" rows="4" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none resize-none"></textarea>
                                @error('description') <span class="mt-1 block text-xs text-rose-500">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="flex items-center justify-end gap-3 border-t border-slate-200 bg-slate-50 px-6 py-4">
                            <button type="button" wire:click="closeModal" class="rounded-full border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-600 transition hover:border-slate-300 hover:text-slate-900">Cancel</button>
                            <button type="submit" class="rounded-full bg-slate-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">{{ $category_id ? 'Update Category' : 'Save Category' }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>

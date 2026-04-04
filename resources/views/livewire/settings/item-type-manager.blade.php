<div class="space-y-6">
    @if (session()->has('message'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">{{ session('message') }}</div>
    @endif

    @if (session()->has('error'))
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700">{{ session('error') }}</div>
    @endif

    <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Inventory Structure</p>
                <h2 class="mt-2 text-2xl font-bold text-slate-900">Item Type Management</h2>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">Organize product families with cleaner labels, active status control, and safer delete handling.</p>
            </div>

            <button wire:click="openModal" class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                <i class="fas fa-layer-group"></i>
                <span>New Item Type</span>
            </button>
        </div>

        <div class="mt-6 grid gap-4 md:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Total Types</p>
                <p class="mt-2 text-3xl font-black text-slate-900">{{ $totalTypes }}</p>
            </div>
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-emerald-500">Active Types</p>
                <p class="mt-2 text-3xl font-black text-emerald-700">{{ $activeTypes }}</p>
            </div>
            <div class="rounded-2xl border border-indigo-200 bg-indigo-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-indigo-500">In Use</p>
                <p class="mt-2 text-3xl font-black text-indigo-700">{{ $itemTypes->getCollection()->filter(fn($type) => $type->stocks->count() > 0)->count() }}</p>
            </div>
        </div>
    </div>

    <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
        <div class="grid gap-4 lg:grid-cols-[minmax(0,1.5fr)_220px]">
            <div>
                <label class="block text-sm font-medium text-slate-700">Search</label>
                <div class="relative mt-2">
                    <i class="fas fa-search pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search item types by name or description..." class="w-full rounded-2xl border-slate-200 pl-11 text-sm shadow-none focus:ring-0">
                </div>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Page Results</p>
                <p class="mt-2 text-2xl font-black text-slate-900">{{ $itemTypes->total() }}</p>
            </div>
        </div>
    </div>

    <div class="overflow-hidden rounded-[1.75rem] border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="border-b border-slate-200 bg-slate-50 text-left text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">
                    <tr>
                        <th class="px-6 py-4">Type</th>
                        <th class="px-6 py-4">Details</th>
                        <th class="px-6 py-4">Products</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($itemTypes as $type)
                        <tr class="bg-white">
                            <td class="px-6 py-4 align-top">
                                <p class="text-sm font-semibold text-slate-900">{{ $type->name }}</p>
                                <p class="mt-1 text-xs text-slate-400">#{{ $type->id }}</p>
                            </td>
                            <td class="px-6 py-4 align-top">
                                <p class="text-sm text-slate-700">{{ $type->slug }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ \Illuminate\Support\Str::limit($type->description ?: 'No description added yet.', 90) }}</p>
                            </td>
                            <td class="px-6 py-4 align-top">
                                <span class="inline-flex rounded-full border border-indigo-200 bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">
                                    {{ $type->stocks->count() }} linked products
                                </span>
                            </td>
                            <td class="px-6 py-4 align-top">
                                <button wire:click="toggleStatus({{ $type->id }})" class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $type->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                    {{ ucfirst($type->status) }}
                                </button>
                            </td>
                            <td class="px-6 py-4 align-top">
                                <div class="flex flex-wrap gap-2">
                                    <button wire:click="edit({{ $type->id }})" class="inline-flex items-center gap-2 rounded-full border border-indigo-200 bg-indigo-50 px-3 py-2 text-xs font-semibold text-indigo-700 transition hover:bg-indigo-100">
                                        <i class="fas fa-pen"></i>
                                        <span>Edit</span>
                                    </button>
                                    <button wire:click="delete({{ $type->id }})" onclick="confirm('Are you sure? This cannot be undone.') || event.stopImmediatePropagation()" class="inline-flex items-center gap-2 rounded-full border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700 transition hover:bg-rose-100">
                                        <i class="fas fa-trash"></i>
                                        <span>Delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center text-sm text-slate-500">No item types match the current search.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>{{ $itemTypes->links() }}</div>

    @if($isOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex min-h-screen items-start justify-center px-4 py-8">
                <div class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm" wire:click="closeModal"></div>
                <div class="relative z-10 w-full max-w-2xl overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-2xl">
                    <form wire:submit="store" class="flex max-h-[90vh] flex-col">
                        <div class="border-b border-slate-200 bg-slate-50 px-6 py-5">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Type Window</p>
                                    <h3 class="mt-2 text-2xl font-bold text-slate-900">{{ $item_type_id ? 'Edit Item Type' : 'Add New Item Type' }}</h3>
                                </div>
                                <button type="button" wire:click="closeModal" class="rounded-full border border-slate-200 p-3 text-slate-500 transition hover:bg-white hover:text-slate-700"><i class="fas fa-xmark"></i></button>
                            </div>
                        </div>

                        <div class="flex-1 overflow-y-auto px-6 py-6">
                            <div class="grid gap-5">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700">Name</label>
                                    <input type="text" wire:model="name" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none">
                                    @error('name') <span class="mt-1 block text-xs text-rose-500">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-700">Description</label>
                                    <textarea wire:model="description" rows="4" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none resize-none"></textarea>
                                    @error('description') <span class="mt-1 block text-xs text-rose-500">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-700">Status</label>
                                    <select wire:model="status" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center justify-end gap-3 border-t border-slate-200 bg-slate-50 px-6 py-4">
                            <button type="button" wire:click="closeModal" class="rounded-full border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-600 transition hover:border-slate-300 hover:text-slate-900">Cancel</button>
                            <button type="submit" class="rounded-full bg-slate-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">{{ $item_type_id ? 'Update Type' : 'Save Type' }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>

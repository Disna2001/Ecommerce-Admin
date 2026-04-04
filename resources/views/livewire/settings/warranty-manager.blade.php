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
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Coverage Workspace</p>
                <h2 class="mt-2 text-2xl font-bold text-slate-900">Warranty Management</h2>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">Maintain manufacturer, store, and extended warranty plans with clearer duration and coverage details.</p>
            </div>

            <button wire:click="openModal" class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                <i class="fas fa-shield-halved"></i>
                <span>New Warranty</span>
            </button>
        </div>

        <div class="mt-6 grid gap-4 md:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Total Warranties</p>
                <p class="mt-2 text-3xl font-black text-slate-900">{{ $totalWarranties }}</p>
            </div>
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-emerald-500">Active Warranties</p>
                <p class="mt-2 text-3xl font-black text-emerald-700">{{ $activeWarranties }}</p>
            </div>
            <div class="rounded-2xl border border-indigo-200 bg-indigo-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-indigo-500">In Use</p>
                <p class="mt-2 text-3xl font-black text-indigo-700">{{ $warranties->getCollection()->filter(fn($warranty) => $warranty->stocks->count() > 0)->count() }}</p>
            </div>
        </div>
    </div>

    <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
        <label class="block text-sm font-medium text-slate-700">Search</label>
        <div class="relative mt-2">
            <i class="fas fa-search pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search warranties by name or type..." class="w-full rounded-2xl border-slate-200 pl-11 text-sm shadow-none focus:ring-0">
        </div>
    </div>

    <div class="overflow-hidden rounded-[1.75rem] border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="border-b border-slate-200 bg-slate-50 text-left text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">
                    <tr>
                        <th class="px-6 py-4">Warranty</th>
                        <th class="px-6 py-4">Coverage</th>
                        <th class="px-6 py-4">Products</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($warranties as $warranty)
                        <tr class="bg-white">
                            <td class="px-6 py-4 align-top">
                                <p class="text-sm font-semibold text-slate-900">{{ $warranty->name }}</p>
                                <p class="mt-1 text-xs uppercase tracking-[0.18em] text-slate-400">{{ ucfirst($warranty->type) }} warranty</p>
                                <p class="mt-2 text-xs font-medium text-indigo-600">{{ $warranty->duration_text }}</p>
                            </td>
                            <td class="px-6 py-4 align-top">
                                <p class="text-sm text-slate-700">{{ \Illuminate\Support\Str::limit($warranty->coverage ?: 'No coverage description added.', 90) }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ \Illuminate\Support\Str::limit($warranty->terms ?: 'No terms added.', 90) }}</p>
                            </td>
                            <td class="px-6 py-4 align-top">
                                <span class="inline-flex rounded-full border border-indigo-200 bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">
                                    {{ $warranty->stocks->count() }} linked products
                                </span>
                            </td>
                            <td class="px-6 py-4 align-top">
                                <button wire:click="toggleStatus({{ $warranty->id }})" class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $warranty->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                    {{ ucfirst($warranty->status) }}
                                </button>
                            </td>
                            <td class="px-6 py-4 align-top">
                                <div class="flex flex-wrap gap-2">
                                    <button wire:click="edit({{ $warranty->id }})" class="inline-flex items-center gap-2 rounded-full border border-indigo-200 bg-indigo-50 px-3 py-2 text-xs font-semibold text-indigo-700 transition hover:bg-indigo-100">
                                        <i class="fas fa-pen"></i>
                                        <span>Edit</span>
                                    </button>
                                    <button wire:click="delete({{ $warranty->id }})" onclick="confirm('Are you sure? This cannot be undone.') || event.stopImmediatePropagation()" class="inline-flex items-center gap-2 rounded-full border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700 transition hover:bg-rose-100">
                                        <i class="fas fa-trash"></i>
                                        <span>Delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center text-sm text-slate-500">No warranties match the current search.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>{{ $warranties->links() }}</div>

    @if($isOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex min-h-screen items-start justify-center px-4 py-8">
                <div class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm" wire:click="closeModal"></div>
                <div class="relative z-10 w-full max-w-3xl overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-2xl">
                    <form wire:submit="store" class="flex max-h-[90vh] flex-col">
                        <div class="border-b border-slate-200 bg-slate-50 px-6 py-5">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Warranty Window</p>
                                    <h3 class="mt-2 text-2xl font-bold text-slate-900">{{ $warranty_id ? 'Edit Warranty' : 'Add New Warranty' }}</h3>
                                </div>
                                <button type="button" wire:click="closeModal" class="rounded-full border border-slate-200 p-3 text-slate-500 transition hover:bg-white hover:text-slate-700"><i class="fas fa-xmark"></i></button>
                            </div>
                        </div>

                        <div class="flex-1 overflow-y-auto px-6 py-6">
                            <div class="grid gap-5 md:grid-cols-2">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700">Name</label>
                                    <input type="text" wire:model="name" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none">
                                    @error('name') <span class="mt-1 block text-xs text-rose-500">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700">Type</label>
                                    <select wire:model="type" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none">
                                        <option value="manufacturer">Manufacturer Warranty</option>
                                        <option value="extended">Extended Warranty</option>
                                        <option value="store">Store Warranty</option>
                                    </select>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-slate-700">Duration (months)</label>
                                    <input type="number" wire:model="duration" min="1" max="120" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none">
                                    @error('duration') <span class="mt-1 block text-xs text-rose-500">{{ $message }}</span> @enderror
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-slate-700">Coverage Details</label>
                                    <textarea wire:model="coverage" rows="4" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none resize-none"></textarea>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-slate-700">Terms & Conditions</label>
                                    <textarea wire:model="terms" rows="4" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none resize-none"></textarea>
                                </div>
                                <div class="md:col-span-2">
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
                            <button type="submit" class="rounded-full bg-slate-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">{{ $warranty_id ? 'Update Warranty' : 'Save Warranty' }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>

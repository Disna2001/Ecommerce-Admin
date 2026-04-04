<div class="space-y-6">
    @if (session()->has('message'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">{{ session('message') }}</div>
    @endif

    <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Brand Origin Workspace</p>
                <h2 class="mt-2 text-2xl font-bold text-slate-900">Make Management</h2>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">Manage manufacturers with clearer country, website, and active state controls for your stock catalog.</p>
            </div>

            <button wire:click="openModal" class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                <i class="fas fa-industry"></i>
                <span>New Make</span>
            </button>
        </div>

        <div class="mt-6 grid gap-4 md:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Visible Results</p>
                <p class="mt-2 text-3xl font-black text-slate-900">{{ $makes->total() }}</p>
            </div>
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-emerald-500">Active Makes</p>
                <p class="mt-2 text-3xl font-black text-emerald-700">{{ $makes->getCollection()->where('is_active', true)->count() }}</p>
            </div>
            <div class="rounded-2xl border border-indigo-200 bg-indigo-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-indigo-500">Rows Per Page</p>
                <p class="mt-2 text-3xl font-black text-indigo-700">{{ $perPage }}</p>
            </div>
        </div>
    </div>

    <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
        <div class="grid gap-4 lg:grid-cols-[minmax(0,1.4fr)_220px]">
            <div>
                <label class="block text-sm font-medium text-slate-700">Search</label>
                <div class="relative mt-2">
                    <i class="fas fa-search pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by make name, code, or country..." class="w-full rounded-2xl border-slate-200 pl-11 text-sm shadow-none focus:ring-0">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700">Page Size</label>
                <select wire:model.live="perPage" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none">
                    <option value="10">10 per page</option>
                    <option value="25">25 per page</option>
                    <option value="50">50 per page</option>
                    <option value="100">100 per page</option>
                </select>
            </div>
        </div>
    </div>

    <div class="overflow-hidden rounded-[1.75rem] border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="border-b border-slate-200 bg-slate-50 text-left text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">
                    <tr>
                        <th wire:click="sortBy('name')" class="cursor-pointer px-6 py-4">Make</th>
                        <th wire:click="sortBy('code')" class="cursor-pointer px-6 py-4">Code</th>
                        <th class="px-6 py-4">Origin</th>
                        <th class="px-6 py-4">Website</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($makes as $make)
                        <tr class="bg-white">
                            <td class="px-6 py-4 align-top">
                                <p class="text-sm font-semibold text-slate-900">{{ $make->name }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ \Illuminate\Support\Str::limit($make->description ?: 'No description added.', 90) }}</p>
                            </td>
                            <td class="px-6 py-4 align-top">
                                <span class="inline-flex rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-700">{{ $make->code }}</span>
                            </td>
                            <td class="px-6 py-4 align-top">
                                <p class="text-sm text-slate-700">{{ $make->country_of_origin ?: 'Not set' }}</p>
                            </td>
                            <td class="px-6 py-4 align-top">
                                @if($make->website)
                                    <a href="{{ $make->website }}" target="_blank" class="text-sm font-medium text-indigo-600 transition hover:text-indigo-800">Visit website</a>
                                @else
                                    <span class="text-sm text-slate-400">No website</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 align-top">
                                <button wire:click="toggleStatus({{ $make->id }})" class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $make->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $make->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </td>
                            <td class="px-6 py-4 align-top">
                                <div class="flex flex-wrap gap-2">
                                    <button wire:click="edit({{ $make->id }})" class="inline-flex items-center gap-2 rounded-full border border-indigo-200 bg-indigo-50 px-3 py-2 text-xs font-semibold text-indigo-700 transition hover:bg-indigo-100">
                                        <i class="fas fa-pen"></i>
                                        <span>Edit</span>
                                    </button>
                                    <button wire:click="delete({{ $make->id }})" onclick="confirm('Are you sure? Deleting this make may affect related stocks.') || event.stopImmediatePropagation()" class="inline-flex items-center gap-2 rounded-full border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700 transition hover:bg-rose-100">
                                        <i class="fas fa-trash"></i>
                                        <span>Delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center text-sm text-slate-500">No makes match the current filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>{{ $makes->links() }}</div>

    @if($isOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex min-h-screen items-start justify-center px-4 py-8">
                <div class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm" wire:click="closeModal"></div>
                <div class="relative z-10 w-full max-w-3xl overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-2xl">
                    <form wire:submit="store" class="flex max-h-[90vh] flex-col">
                        <div class="border-b border-slate-200 bg-slate-50 px-6 py-5">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Make Window</p>
                                    <h3 class="mt-2 text-2xl font-bold text-slate-900">{{ $make_id ? 'Edit Make' : 'Add New Make' }}</h3>
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
                                    <label class="block text-sm font-medium text-slate-700">Code</label>
                                    <input type="text" wire:model="code" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none">
                                    @error('code') <span class="mt-1 block text-xs text-rose-500">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700">Country of Origin</label>
                                    <input type="text" wire:model="country_of_origin" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700">Website</label>
                                    <input type="url" wire:model="website" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none" placeholder="https://example.com">
                                    @error('website') <span class="mt-1 block text-xs text-rose-500">{{ $message }}</span> @enderror
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-slate-700">Description</label>
                                    <textarea wire:model="description" rows="4" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none resize-none"></textarea>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="inline-flex items-center gap-3 rounded-full border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-700">
                                        <input type="checkbox" wire:model="is_active" class="rounded border-slate-300 text-slate-900 focus:ring-slate-500">
                                        Keep this make active
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center justify-end gap-3 border-t border-slate-200 bg-slate-50 px-6 py-4">
                            <button type="button" wire:click="closeModal" class="rounded-full border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-600 transition hover:border-slate-300 hover:text-slate-900">Cancel</button>
                            <button type="submit" class="rounded-full bg-slate-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">{{ $make_id ? 'Update Make' : 'Save Make' }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>

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
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Brand Library</p>
                <h2 class="mt-2 text-2xl font-bold text-slate-900">Brand Management</h2>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">Manage brand identity, status, website links, and logo assets in a cleaner, easier workflow.</p>
            </div>
            <button wire:click="openModal" class="inline-flex items-center gap-2 rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                <i class="fas fa-plus"></i>
                <span>New Brand</span>
            </button>
        </div>

        <div class="mt-6 grid gap-4 md:grid-cols-4">
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Total Brands</p>
                <p class="mt-2 text-3xl font-black text-slate-900">{{ $totalBrands }}</p>
            </div>
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-emerald-500">Active</p>
                <p class="mt-2 text-3xl font-black text-emerald-700">{{ $activeBrands }}</p>
            </div>
            <div class="rounded-2xl border border-indigo-200 bg-indigo-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-indigo-500">Using Logos</p>
                <p class="mt-2 text-3xl font-black text-indigo-700">{{ $brandsWithLogos }}</p>
            </div>
            <div class="rounded-2xl border border-sky-200 bg-sky-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-sky-500">With Products</p>
                <p class="mt-2 text-3xl font-black text-sky-700">{{ $brandsWithProducts }}</p>
            </div>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
        <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
            <label class="block text-sm font-medium text-slate-700">Search Brands</label>
            <div class="relative mt-2">
                <i class="fas fa-search pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search brands by name or description..." class="w-full rounded-2xl border-slate-200 pl-11 text-sm shadow-none focus:ring-0">
            </div>
        </div>

        <div class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-start gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-violet-50 text-violet-600">
                    <i class="fas fa-certificate"></i>
                </div>
                <div>
                    <h3 class="text-xl font-semibold text-slate-900">Brand Library Tips</h3>
                    <p class="mt-1 text-sm text-slate-500">Use official brand names, keep websites valid, and remove logos only when they are truly obsolete.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="rounded-[1.75rem] border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="border-b border-slate-200 bg-slate-50 text-left text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">
                    <tr>
                        <th class="px-6 py-4">Brand</th>
                        <th class="px-6 py-4">Logo</th>
                        <th class="px-6 py-4">Description</th>
                        <th class="px-6 py-4">Website</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($brands as $brand)
                        <tr>
                            <td class="px-6 py-4">
                                <p class="text-sm font-semibold text-slate-900">{{ $brand->name }}</p>
                                <p class="mt-1 text-xs text-slate-400">{{ $brand->slug }}</p>
                            </td>
                            <td class="px-6 py-4">
                                @if($brand->logo)
                                    <img src="{{ Storage::url($brand->logo) }}" class="h-12 w-12 rounded-2xl object-cover">
                                @else
                                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                                        <i class="fas fa-image"></i>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-500">
                                {{ \Illuminate\Support\Str::limit($brand->description, 80) ?: 'No description added.' }}
                                <p class="mt-2 text-xs text-slate-400">{{ $brand->stocks_count }} product link(s)</p>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                @if($brand->website)
                                    <a href="{{ $brand->website }}" target="_blank" class="text-indigo-600 hover:text-indigo-800">{{ $brand->website }}</a>
                                @else
                                    <span class="text-slate-400">No website</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <button wire:click="toggleStatus({{ $brand->id }})" class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $brand->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                    {{ ucfirst($brand->status) }}
                                </button>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-2">
                                    <button wire:click="edit({{ $brand->id }})" class="inline-flex items-center gap-2 rounded-full border border-indigo-200 bg-indigo-50 px-3 py-2 text-xs font-semibold text-indigo-700 transition hover:bg-indigo-100">
                                        <i class="fas fa-pen"></i> Edit
                                    </button>
                                    <button wire:click="delete({{ $brand->id }})" onclick="confirm('Delete this brand?') || event.stopImmediatePropagation()" class="inline-flex items-center gap-2 rounded-full border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700 transition hover:bg-rose-100">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-16 text-center text-sm text-slate-500">No brands found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>{{ $brands->links() }}</div>

    @if($isOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex min-h-screen items-start justify-center px-4 py-8">
                <div class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm" wire:click="closeModal"></div>
                <div class="relative z-10 w-full max-w-3xl overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-2xl">
                    <form wire:submit="store">
                        <div class="border-b border-slate-200 bg-slate-50 px-6 py-5">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Brand Window</p>
                                    <h3 class="mt-2 text-2xl font-bold text-slate-900">{{ $brand_id ? 'Edit Brand' : 'Add New Brand' }}</h3>
                                </div>
                                <button type="button" wire:click="closeModal" class="rounded-full border border-slate-200 p-3 text-slate-500 transition hover:bg-white hover:text-slate-700"><i class="fas fa-xmark"></i></button>
                            </div>
                        </div>

                        <div class="grid gap-6 px-6 py-6 lg:grid-cols-2">
                            <div class="space-y-5">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700">Brand Name</label>
                                    <input type="text" wire:model="name" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none">
                                    @error('name') <span class="mt-1 block text-xs text-rose-500">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700">Description</label>
                                    <textarea wire:model="description" rows="4" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none resize-none"></textarea>
                                    @error('description') <span class="mt-1 block text-xs text-rose-500">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700">Website</label>
                                    <input type="text" wire:model="website" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none" placeholder="https://example.com">
                                    @error('website') <span class="mt-1 block text-xs text-rose-500">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700">Status</label>
                                    <select wire:model="status" class="mt-2 w-full rounded-2xl border-slate-200 text-sm shadow-none">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>

                            <div class="space-y-5">
                                <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-5">
                                    <h4 class="text-base font-semibold text-slate-900">Brand Logo</h4>
                                    <input type="file" wire:model="logo" accept="image/*" class="mt-4 block w-full text-sm text-slate-600">
                                    @error('logo') <span class="mt-1 block text-xs text-rose-500">{{ $message }}</span> @enderror

                                    @if($logo && !$errors->has('logo'))
                                        <div class="mt-4">
                                            <img src="{{ $logo->temporaryUrl() }}" class="h-24 w-24 rounded-2xl object-cover">
                                            <p class="mt-2 text-xs text-slate-400">New logo preview</p>
                                        </div>
                                    @elseif($currentLogoPath)
                                        <div class="mt-4">
                                            <img src="{{ Storage::url($currentLogoPath) }}" class="h-24 w-24 rounded-2xl object-cover">
                                            <p class="mt-2 text-xs text-slate-400">Current logo</p>
                                            <button type="button" wire:click="removeCurrentLogo" class="mt-3 inline-flex items-center gap-2 rounded-full border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700 transition hover:bg-rose-100">
                                                <i class="fas fa-trash"></i> Remove Logo
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3 border-t border-slate-200 bg-slate-50 px-6 py-4">
                            <button type="button" wire:click="closeModal" class="rounded-full border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-600 transition hover:border-slate-300 hover:text-slate-900">Cancel</button>
                            <button type="submit" class="rounded-full bg-slate-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">{{ $brand_id ? 'Update Brand' : 'Save Brand' }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>

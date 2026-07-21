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
                <h2 class="mt-1 text-xl font-bold text-slate-900">Brand Management</h2>
                <p class="mt-1 text-xs text-slate-500">Manage brand identity, logos, and website links for product cataloging.</p>
            </div>

            <button wire:click="openModal" class="inline-flex items-center gap-2 rounded-lg bg-slate-900 px-4 py-2 text-xs font-semibold text-white transition hover:bg-slate-800 shadow-xs">
                <i class="fas fa-plus text-xs"></i>
                <span>New Brand</span>
            </button>
        </div>

        <!-- Consistent Stat Cards KPI Row -->
        <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <x-admin.dashboard.stat-card label="Total Brands" :value="$totalBrands" icon="fa-copyright" tone="indigo" />
            <x-admin.dashboard.stat-card label="Active" :value="$activeBrands" icon="fa-circle-check" tone="emerald" />
            <x-admin.dashboard.stat-card label="With Products" :value="$brandsWithProducts" icon="fa-boxes-stacked" tone="slate" />
            <x-admin.dashboard.stat-card label="With Logos" :value="$brandsWithLogos" icon="fa-image" tone="amber" />
        </div>
    </div>

    <!-- Search & Guidance Grid -->
    <div class="grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-xs">
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-400">Search Brands</label>
            <div class="relative mt-2">
                <i class="fas fa-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by brand name or description..." class="w-full rounded-lg border-slate-200 pl-9 text-xs font-semibold shadow-xs focus:border-slate-900 focus:ring-0">
            </div>
        </div>

        <x-admin.catalog.guidance-panel 
            title="Brand Guidance" 
            tip="Keep brand names consistent with how customers search — avoid abbreviations that don't match common usage." 
            icon="fa-copyright" 
        />
    </div>

    <!-- Table -->
    <div class="rounded-xl border border-slate-200 bg-white shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50 text-left text-[10px] font-bold uppercase tracking-wider text-slate-400">
                    <tr>
                        <th class="px-6 py-3.5">Brand</th>
                        <th class="px-6 py-3.5">Logo</th>
                        <th class="px-6 py-3.5">Description</th>
                        <th class="px-6 py-3.5">Website</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white text-xs">
                    @forelse($brands as $brand)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-900">{{ $brand->name }}</p>
                                <p class="text-[10px] font-medium text-slate-400">{{ $brand->slug }}</p>
                            </td>
                            <td class="px-6 py-4">
                                @if($brand->logo)
                                    <img src="{{ Storage::url($brand->logo) }}" class="h-9 w-9 rounded-lg object-cover border border-slate-200">
                                @else
                                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 text-slate-400">
                                        <i class="fas fa-image text-xs"></i>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-500">
                                {{ \Illuminate\Support\Str::limit($brand->description, 80) ?: 'No description added.' }}
                                <p class="mt-1 text-[10px] font-semibold text-slate-400">{{ $brand->stocks_count }} product(s)</p>
                            </td>
                            <td class="px-6 py-4">
                                @if($brand->website)
                                    <a href="{{ $brand->website }}" target="_blank" class="text-indigo-600 hover:text-indigo-800 font-semibold">{{ $brand->website }}</a>
                                @else
                                    <span class="text-slate-400">No website</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <button wire:click="toggleStatus({{ $brand->id }})" class="inline-flex rounded-md px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider {{ $brand->status === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                                    {{ ucfirst($brand->status) }}
                                </button>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button wire:click="edit({{ $brand->id }})" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-50 hover:text-slate-900 shadow-xs">
                                        <i class="fas fa-pen text-[10px] text-slate-400"></i> Edit
                                    </button>
                                    <button wire:click="delete({{ $brand->id }})" onclick="confirm('Delete this brand?') || event.stopImmediatePropagation()" class="inline-flex items-center gap-1.5 rounded-lg border border-rose-200 bg-rose-50 px-2.5 py-1.5 text-xs font-semibold text-rose-600 transition hover:bg-rose-100 shadow-xs">
                                        <i class="fas fa-trash text-[10px]"></i> Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-400 font-medium">No brands found matching your search.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($brands->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $brands->links() }}
            </div>
        @endif
    </div>

    <!-- Brand Modal -->
    @if($isOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
            <div class="w-full max-w-lg rounded-xl border border-slate-200 bg-white p-6 shadow-xl space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <h3 class="text-base font-bold text-slate-900">{{ $brand_id ? 'Edit Brand' : 'New Brand' }}</h3>
                    <button wire:click="closeModal" class="text-slate-400 hover:text-slate-600"><i class="fas fa-times"></i></button>
                </div>

                <form wire:submit.prevent="store" class="space-y-4 text-xs">
                    <div class="space-y-1">
                        <label class="block font-bold text-slate-700">Brand Name</label>
                        <input type="text" wire:model="name" class="w-full rounded-lg border-slate-200 px-3 py-2 font-semibold text-slate-900 focus:ring-0">
                        @error('name') <span class="text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="block font-bold text-slate-700">Description</label>
                        <textarea wire:model="description" rows="3" class="w-full rounded-lg border-slate-200 px-3 py-2 font-medium text-slate-900 focus:ring-0"></textarea>
                    </div>

                    <div class="space-y-1">
                        <label class="block font-bold text-slate-700">Website</label>
                        <input type="text" wire:model="website" placeholder="https://example.com" class="w-full rounded-lg border-slate-200 px-3 py-2 font-semibold text-slate-900 focus:ring-0">
                        @error('website') <span class="text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1">
                        <label class="block font-bold text-slate-700">Status</label>
                        <select wire:model="status" class="w-full rounded-lg border-slate-200 px-3 py-2 font-semibold text-slate-900 focus:ring-0">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    <div class="space-y-2 pt-2 border-t border-slate-100">
                        <label class="block font-bold text-slate-700">Brand Logo</label>
                        <input type="file" wire:model="logo" accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
                        @error('logo') <span class="text-rose-500">{{ $message }}</span> @enderror

                        @if($logo && !$errors->has('logo'))
                            <div class="mt-2 flex items-center gap-3">
                                <img src="{{ $logo->temporaryUrl() }}" class="h-12 w-12 rounded-lg object-cover border border-slate-200">
                                <span class="text-[10px] font-semibold text-emerald-600">New logo selected</span>
                            </div>
                        @elseif($currentLogoPath)
                            <div class="mt-2 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <img src="{{ Storage::url($currentLogoPath) }}" class="h-12 w-12 rounded-lg object-cover border border-slate-200">
                                    <span class="text-[10px] font-semibold text-slate-500">Current logo</span>
                                </div>
                                <button type="button" wire:click="removeCurrentLogo" class="text-xs font-semibold text-rose-600 hover:text-rose-800">Remove</button>
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button" wire:click="closeModal" class="rounded-lg border border-slate-200 bg-white px-4 py-2 font-semibold text-slate-700 hover:bg-slate-50">Cancel</button>
                        <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 font-semibold text-white hover:bg-slate-800 shadow-xs">{{ $brand_id ? 'Update Brand' : 'Create Brand' }}</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

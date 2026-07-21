<div class="space-y-6">
    @if (session()->has('message'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs font-semibold text-emerald-700">{{ session('message') }}</div>
    @endif

    <!-- Content Header & Actions -->
    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-xs">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Catalog Setup</p>
                <h2 class="mt-1 text-xl font-bold text-slate-900">Quality Tier Management</h2>
                <p class="mt-1 text-xs text-slate-500">Manage quality tiers (OEM, Aftermarket, Rebuilt) to communicate condition and origin.</p>
            </div>

            <button wire:click="openModal" class="inline-flex items-center gap-2 rounded-lg bg-slate-900 px-4 py-2 text-xs font-semibold text-white transition hover:bg-slate-800 shadow-xs">
                <i class="fas fa-plus text-xs"></i>
                <span>New Quality Tier</span>
            </button>
        </div>

        <!-- Consistent Stat Cards KPI Row -->
        <div class="mt-6 grid gap-4 sm:grid-cols-3">
            <x-admin.dashboard.stat-card label="Total Tiers" :value="$totalTiers" icon="fa-award" tone="indigo" />
            <x-admin.dashboard.stat-card label="Active" :value="$activeTiers" icon="fa-circle-check" tone="emerald" />
            <x-admin.dashboard.stat-card label="With Products" :value="$tiersWithProducts" icon="fa-boxes-stacked" tone="slate" />
        </div>
    </div>

    <!-- Search & Guidance Grid -->
    <div class="grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-xs">
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-400">Search Quality Tiers</label>
            <div class="relative mt-2">
                <i class="fas fa-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by tier name, code, or description..." class="w-full rounded-lg border-slate-200 pl-9 text-xs font-semibold shadow-xs focus:border-slate-900 focus:ring-0">
            </div>
        </div>

        <x-admin.catalog.guidance-panel 
            title="Quality Tier Guidance" 
            tip="Quality tier should reflect sourcing reality — this affects customer trust and return rates, keep definitions consistent across intake staff." 
            icon="fa-award" 
        />
    </div>

    <!-- Table -->
    <div class="rounded-xl border border-slate-200 bg-white shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50 text-left text-[10px] font-bold uppercase tracking-wider text-slate-400">
                    <tr>
                        <th wire:click="sortBy('level_order')" class="cursor-pointer px-6 py-3.5">Order</th>
                        <th wire:click="sortBy('name')" class="cursor-pointer px-6 py-3.5">Tier</th>
                        <th wire:click="sortBy('code')" class="cursor-pointer px-6 py-3.5">Code</th>
                        <th class="px-6 py-3.5">Description</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white text-xs">
                    @forelse($qualities as $quality)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4 font-bold text-slate-700">#{{ $quality->level_order }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="h-3 w-3 rounded-full border border-slate-200 shrink-0" style="background-color: {{ $quality->color ?: '#6B7280' }}"></span>
                                    <p class="font-bold text-slate-900">{{ $quality->name }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-mono text-[11px]">
                                <span class="inline-flex rounded-md bg-slate-100 px-2.5 py-0.5 font-bold text-slate-700 border border-slate-200">{{ $quality->code }}</span>
                            </td>
                            <td class="px-6 py-4 text-slate-500">{{ \Illuminate\Support\Str::limit($quality->description, 80) ?: 'No description added.' }}</td>
                            <td class="px-6 py-4">
                                <button wire:click="toggleStatus({{ $quality->id }})" class="inline-flex rounded-md px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider {{ $quality->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                                    {{ $quality->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button wire:click="edit({{ $quality->id }})" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-50 hover:text-slate-900 shadow-xs">
                                        <i class="fas fa-pen text-[10px] text-slate-400"></i> Edit
                                    </button>
                                    <button wire:click="delete({{ $quality->id }})" onclick="confirm('Delete this quality tier?') || event.stopImmediatePropagation()" class="inline-flex items-center gap-1.5 rounded-lg border border-rose-200 bg-rose-50 px-2.5 py-1.5 text-xs font-semibold text-rose-600 transition hover:bg-rose-100 shadow-xs">
                                        <i class="fas fa-trash text-[10px]"></i> Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-400 font-medium">No quality tiers found matching your search.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($qualities->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $qualities->links() }}
            </div>
        @endif
    </div>

    <!-- Quality Level Modal -->
    @if($isOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
            <div class="w-full max-w-lg rounded-xl border border-slate-200 bg-white p-6 shadow-xl space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <h3 class="text-base font-bold text-slate-900">{{ $quality_id ? 'Edit Quality Tier' : 'New Quality Tier' }}</h3>
                    <button wire:click="closeModal" class="text-slate-400 hover:text-slate-600"><i class="fas fa-times"></i></button>
                </div>

                <form wire:submit.prevent="store" class="space-y-4 text-xs">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="space-y-1">
                            <label class="block font-bold text-slate-700">Tier Name</label>
                            <input type="text" wire:model="name" class="w-full rounded-lg border-slate-200 px-3 py-2 font-semibold text-slate-900 focus:ring-0">
                            @error('name') <span class="text-rose-500">{{ $message }}</span> @enderror
                        </div>
                        <div class="space-y-1">
                            <label class="block font-bold text-slate-700">Code</label>
                            <input type="text" wire:model="code" placeholder="oem" class="w-full rounded-lg border-slate-200 px-3 py-2 font-semibold text-slate-900 focus:ring-0">
                            @error('code') <span class="text-rose-500">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="space-y-1">
                            <label class="block font-bold text-slate-700">Display Order</label>
                            <input type="number" wire:model="level_order" min="0" class="w-full rounded-lg border-slate-200 px-3 py-2 font-semibold text-slate-900 focus:ring-0">
                            @error('level_order') <span class="text-rose-500">{{ $message }}</span> @enderror
                        </div>
                        <div class="space-y-1">
                            <label class="block font-bold text-slate-700">Color</label>
                            <div class="flex gap-2">
                                <input type="color" wire:model="color" class="h-9 w-10 rounded-lg border border-slate-200 bg-white p-1">
                                <input type="text" wire:model="color" placeholder="#6B7280" class="flex-1 rounded-lg border-slate-200 px-3 py-2 font-semibold text-slate-900 focus:ring-0">
                            </div>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="block font-bold text-slate-700">Description</label>
                        <textarea wire:model="description" rows="3" class="w-full rounded-lg border-slate-200 px-3 py-2 font-medium text-slate-900 focus:ring-0"></textarea>
                    </div>

                    <div class="pt-2">
                        <label class="inline-flex items-center gap-2 font-semibold text-slate-700">
                            <input type="checkbox" wire:model="is_active" class="rounded border-slate-300 text-slate-900 focus:ring-0">
                            Keep this tier active
                        </label>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button" wire:click="closeModal" class="rounded-lg border border-slate-200 bg-white px-4 py-2 font-semibold text-slate-700 hover:bg-slate-50">Cancel</button>
                        <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 font-semibold text-white hover:bg-slate-800 shadow-xs">{{ $quality_id ? 'Update Tier' : 'Create Tier' }}</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

<div class="relative">
    <div x-data="{ show:false, message:'', type:'success' }"
         x-on:notify.window="show=true; message=$event.detail.message; type=$event.detail.type; setTimeout(()=>show=false,3200)"
         x-show="show" x-transition
         class="fixed bottom-5 right-5 z-[90] flex items-center gap-2 rounded-2xl px-5 py-3 text-sm font-semibold text-white shadow-xl"
         :class="type==='success' ? 'bg-emerald-500' : (type==='error' ? 'bg-red-500' : 'bg-violet-500')"
         style="display:none">
        <i class="fas" :class="type==='success' ? 'fa-check-circle' : (type==='error' ? 'fa-times-circle' : 'fa-info-circle')"></i>
        <span x-text="message"></span>
    </div>

    @php
        $tabs = [
            'overview' => ['label' => 'Intel', 'icon' => 'fa-user'],
            'orders' => ['label' => 'Ledger', 'icon' => 'fa-bag-shopping'],
            'addresses' => ['label' => 'Geospatial', 'icon' => 'fa-location-dot'],
            'wishlist' => ['label' => 'Saved', 'icon' => 'fa-heart'],
            'reviews' => ['label' => 'Audits', 'icon' => 'fa-star'],
            'settings' => ['label' => 'Config', 'icon' => 'fa-sliders'],
            'security' => ['label' => 'Firewall', 'icon' => 'fa-shield-halved'],
        ];
        $panel = 'premium-card p-10';
        $input = 'w-full rounded-2xl border border-slate-200/50 bg-white/50 px-6 py-4 text-xs font-bold uppercase tracking-widest text-slate-700 outline-none transition-all duration-500 focus:border-[var(--primary)] focus:bg-white focus:ring-8 focus:ring-[var(--primary)]/5 dark:border-white/5 dark:bg-slate-900/50 dark:text-white';
        $muted = 'text-slate-400 font-bold uppercase tracking-widest text-[9px]';
        $defaultAddress = $addresses->firstWhere('is_default', true) ?? $addresses->first();
    @endphp

    <div class="mx-auto max-w-7xl px-4 py-6 sm:py-12">
        <section class="overflow-hidden rounded-[2.5rem] sm:rounded-[3.5rem] border border-white/60 bg-white/80 shadow-[0_40px_120px_rgba(15,23,42,0.08)] backdrop-blur dark:border-slate-800 dark:bg-slate-950/90">
            <div class="border-b border-slate-100 px-4 sm:px-8 py-6 sm:py-10 dark:border-white/5 lg:px-12 lg:py-12">
                <div class="grid gap-12 xl:grid-cols-[minmax(0,1fr)_340px] xl:items-center">
                    <div class="flex flex-col gap-8 sm:flex-row sm:items-center">
                        <div class="relative h-32 w-32 flex-shrink-0 overflow-hidden rounded-[3rem] border-4 border-white shadow-2xl dark:border-slate-800">
                            @if($profile_photo)
                                <img src="{{ $profile_photo->temporaryUrl() }}" alt="{{ $user->name }}" class="h-full w-full object-cover">
                            @elseif($user->profile_photo_path ?? null)
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($user->profile_photo_path) }}" alt="{{ $user->name }}" class="h-full w-full object-cover">
                            @else
                                <div class="flex h-full w-full items-center justify-center text-5xl font-black text-white" style="background: linear-gradient(135deg, var(--primary), var(--secondary))">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                            @endif
                        </div>

                        <div class="min-w-0 flex-1">
                            <p class="text-[10px] font-black uppercase tracking-[0.4em] text-[var(--primary)] mb-3">Customer Registry</p>
                            <h1 class="text-4xl font-black text-slate-900 dark:text-white tracking-tight sm:text-5xl">{{ $user->name }}</h1>
                            <p class="mt-4 truncate text-sm font-bold text-slate-400 uppercase tracking-widest">{{ $user->email }}</p>
                            <div class="mt-8 flex flex-wrap gap-4">
                                <span class="rounded-full bg-slate-50 border border-slate-100 px-4 py-2 text-[10px] font-black uppercase tracking-widest text-slate-500 dark:bg-white/5 dark:border-white/5">{{ $orders->count() }} Orders</span>
                                <span class="rounded-full bg-slate-50 border border-slate-100 px-4 py-2 text-[10px] font-black uppercase tracking-widest text-slate-500 dark:bg-white/5 dark:border-white/5">{{ $wishlistProducts->count() }} Saved</span>
                                @can('view-admin-menu')
                                    <span class="rounded-full px-4 py-2 text-[10px] font-black uppercase tracking-widest text-white shadow-lg" style="background: linear-gradient(90deg, var(--primary), var(--secondary))">Admin Authority</span>
                                @endcan
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-4">
                        <div class="premium-card p-6 !rounded-[2rem]">
                            <p class="text-[9px] font-black uppercase tracking-[0.3em] text-slate-400 mb-2">Active Geospatial</p>
                            <p class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest">{{ $defaultAddress?->city ?? 'Registry Empty' }}</p>
                            <p class="mt-2 text-[11px] font-bold text-slate-400 leading-relaxed">{{ $defaultAddress?->address ?? 'Add an address to complete the profile.' }}</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <label class="flex-1 flex cursor-pointer items-center justify-center gap-3 rounded-full border border-slate-200 bg-white px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-900 shadow-sm transition-all hover:bg-slate-50 dark:border-white/5 dark:bg-slate-900 dark:text-white">
                                <i class="fas fa-camera text-xs text-slate-400"></i>
                                Update Image
                                <input type="file" wire:model="profile_photo" accept="image/*" class="hidden">
                            </label>
                            @if($profile_photo)
                                <button type="button" wire:click="savePhoto" class="rounded-full bg-[var(--primary)] px-8 py-4 text-[10px] font-black uppercase tracking-widest text-white shadow-xl">Commit</button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-b border-slate-50 px-4 sm:px-8 py-3 sm:py-4 dark:border-white/5 lg:px-12">
                <div class="flex flex-wrap gap-2 sm:gap-4">
                    @foreach($tabs as $key => $tab)
                        <button type="button" wire:click="setTab('{{ $key }}')"
                                class="inline-flex items-center gap-2 sm:gap-3 rounded-full px-4 sm:px-8 py-2.5 sm:py-4 text-[10px] font-black uppercase tracking-[0.2em] transition-all {{ $activeTab === $key ? 'bg-slate-900 text-white shadow-2xl scale-105' : 'text-slate-400 hover:text-slate-900' }}">
                            <i class="fas {{ $tab['icon'] }} text-xs"></i>
                            <span>{{ $tab['label'] }}</span>
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="px-4 sm:px-8 py-6 sm:py-10 lg:px-12 lg:py-12">
                @if($activeTab === 'overview')
                    <div class="space-y-12">
                        <div class="grid gap-8 lg:grid-cols-3">
                            <article class="{{ $panel }}">
                                <p class="{{ $muted }} mb-6">Contact Matrix</p>
                                <p class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">{{ $user->phone ?: 'Not Established' }}</p>
                                <p class="mt-4 text-[11px] font-bold text-slate-400 leading-relaxed">{{ $user->address ?: 'Registry entry missing. Update contact matrix for optimal deployment.' }}</p>
                            </article>
                            <article class="{{ $panel }}">
                                <p class="{{ $muted }} mb-6">Ledger Activity</p>
                                <p class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">{{ optional($orders->first())->order_number ?? 'Zero Activity' }}</p>
                                <p class="mt-4 text-[11px] font-bold text-slate-400 leading-relaxed">{{ $orders->whereIn('status', ['pending','processing','shipped'])->count() }} Active protocols currently in execution phase.</p>
                            </article>
                            <article class="{{ $panel }}">
                                <p class="{{ $muted }} mb-6">Geospatial Nodes</p>
                                <p class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">{{ $addresses->count() }} Registered</p>
                                <p class="mt-4 text-[11px] font-bold text-slate-400 leading-relaxed">Saved geospatial nodes for rapid deployment across the logistics network.</p>
                                <button type="button" wire:click="setTab('addresses')" class="mt-6 text-[10px] font-black uppercase tracking-widest text-[var(--primary)] hover:underline">Manage Nodes</button>
                            </article>
                        </div>

                        <div class="grid gap-12 xl:grid-cols-[1.2fr_0.8fr]">
                            <section class="{{ $panel }}">
                                <div class="flex items-center justify-between mb-10">
                                    <div>
                                        <p class="{{ $muted }} mb-2">Recent Ledger</p>
                                        <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Account Activity</h2>
                                    </div>
                                    <button type="button" wire:click="setTab('orders')" class="h-10 px-6 flex items-center justify-center rounded-full bg-slate-50 text-[9px] font-black uppercase tracking-widest text-slate-500 hover:bg-slate-100 transition-all">View All</button>
                                </div>
                                <div class="space-y-4">
                                    @forelse($orders->take(4) as $order)
                                        <div class="flex flex-col gap-4 rounded-3xl border border-slate-50 p-6 dark:border-white/5 sm:flex-row sm:items-center sm:justify-between hover:bg-slate-50/50 transition-colors">
                                            <div>
                                                <p class="text-xs font-black text-slate-900 dark:text-white tracking-widest">#{{ $order->order_number ?? $order->id }}</p>
                                                <p class="mt-1 text-[9px] font-black text-slate-400 uppercase tracking-widest">{{ $order->created_at->format('M d, Y') }}</p>
                                            </div>
                                            <div class="flex flex-wrap items-center gap-4">
                                                <span class="rounded-full bg-slate-100 px-3 py-1 text-[9px] font-black uppercase tracking-widest text-slate-500">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span>
                                                <span class="text-sm font-black text-slate-900 dark:text-white">Rs {{ number_format($order->total ?? 0, 2) }}</span>
                                                <a wire:navigate href="{{ route('orders.show', $order) }}" class="h-8 w-8 flex items-center justify-center rounded-full bg-white shadow-sm border border-slate-100 text-slate-400 hover:text-[var(--primary)] transition-all"><i class="fas fa-arrow-right text-[10px]"></i></a>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="py-12 text-center glass rounded-[2.5rem]">
                                            <i class="fas fa-database text-2xl text-slate-200 mb-4"></i>
                                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Ledger Empty</p>
                                        </div>
                                    @endforelse
                                </div>
                            </section>

                            <section class="{{ $panel }}">
                                <p class="{{ $muted }} mb-2">Snapshot</p>
                                <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight mb-10">Registry Overview</h2>
                                <div class="space-y-6">
                                    @foreach([
                                        ['Geospatial Node', $defaultAddress?->address ? $defaultAddress->address.', '.$defaultAddress->city : 'Empty'],
                                        ['Audit History', $reviews->count().' Submissions'],
                                        ['Registry Reserves', $wishlistProducts->count().' Saved Items']
                                    ] as [$label,$value])
                                        <div class="rounded-3xl bg-slate-50/50 p-6 dark:bg-white/5">
                                            <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-2">{{ $label }}</p>
                                            <p class="text-xs font-black text-slate-900 dark:text-white leading-relaxed">{{ $value }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            </section>
                        </div>
                    </div>
                @endif

                @if($activeTab === 'addresses')
                    <section class="space-y-12">
                        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                            <div class="max-w-2xl">
                                <p class="{{ $muted }} mb-3">Geospatial Protocol</p>
                                <h2 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Saved Distribution Nodes</h2>
                                <p class="mt-4 text-sm font-bold text-slate-400 leading-relaxed">Your default node will be utilized as the primary destination for all logistics operations during the acquisition phase.</p>
                            </div>
                            <button type="button" wire:click="$toggle('showAddressForm')" class="h-14 px-8 flex items-center justify-center rounded-full bg-slate-900 text-[10px] font-black uppercase tracking-widest text-white shadow-2xl transition-all hover:scale-105">
                                {{ $showAddressForm ? 'Terminate Protocol' : 'Initialize New Node' }}
                            </button>
                        </div>

                        @if($showAddressForm)
                            <div class="{{ $panel }} animate-in slide-in-from-top duration-500">
                                <h3 class="text-xl font-black text-slate-900 dark:text-white mb-8">Node Configuration</h3>
                                <div class="grid gap-8 md:grid-cols-2">
                                    <div class="space-y-2">
                                        <label class="{{ $muted }}">Entity Name</label>
                                        <input wire:model="addr_name" type="text" class="{{ $input }}" placeholder="Recipient Full Name">
                                        @error('addr_name')<p class="mt-1 text-[9px] font-black uppercase text-rose-500">{{ $message }}</p>@enderror
                                    </div>
                                    <div class="space-y-2">
                                        <label class="{{ $muted }}">Contact Line</label>
                                        <input wire:model="addr_phone" type="text" class="{{ $input }}" placeholder="+94 XX XXX XXXX">
                                        @error('addr_phone')<p class="mt-1 text-[9px] font-black uppercase text-rose-500">{{ $message }}</p>@enderror
                                    </div>
                                    <div class="md:col-span-2 space-y-2">
                                        <label class="{{ $muted }}">Geospatial Coordinates (Street Address)</label>
                                        <input wire:model="addr_address" type="text" class="{{ $input }}" placeholder="Full Street Address Details">
                                        @error('addr_address')<p class="mt-1 text-[9px] font-black uppercase text-rose-500">{{ $message }}</p>@enderror
                                    </div>
                                    <div class="space-y-2">
                                        <label class="{{ $muted }}">Urban Sector (City)</label>
                                        <input wire:model="addr_city" type="text" class="{{ $input }}" placeholder="Colombo / Gampaha / etc.">
                                        @error('addr_city')<p class="mt-1 text-[9px] font-black uppercase text-rose-500">{{ $message }}</p>@enderror
                                    </div>
                                    <div class="space-y-2">
                                        <label class="{{ $muted }}">Postal Index</label>
                                        <input wire:model="addr_postal" type="text" class="{{ $input }}" placeholder="XXXXX">
                                    </div>
                                </div>
                                <div class="mt-10 flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                                    <label class="flex items-center gap-4 cursor-pointer">
                                        <input wire:model="addr_is_default" type="checkbox" class="w-5 h-5 rounded-lg border-slate-200 text-slate-900 focus:ring-0">
                                        <span class="text-[10px] font-black uppercase tracking-widest text-slate-500">Designate as Primary Protocol Node</span>
                                    </label>
                                    <button type="button" wire:click="saveAddress" class="h-14 px-12 rounded-full bg-[var(--primary)] text-[10px] font-black uppercase tracking-widest text-white shadow-xl hover:scale-105 transition-all">Commit Node</button>
                                </div>
                            </div>
                        @endif

                        <div class="grid gap-8 lg:grid-cols-2">
                            @forelse($addresses as $savedAddress)
                                <div class="{{ $panel }} group hover:border-[var(--primary)] transition-all">
                                    <div class="flex items-start justify-between mb-6">
                                        <div class="flex items-center gap-3">
                                            <div class="h-10 w-10 flex items-center justify-center rounded-full bg-slate-50 text-slate-400 dark:bg-white/5"><i class="fas fa-location-dot text-xs"></i></div>
                                            <div>
                                                <p class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-widest">{{ $savedAddress->name }}</p>
                                                @if($savedAddress->is_default)
                                                    <span class="text-[8px] font-black uppercase tracking-[0.2em] text-emerald-500">Primary Node</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="space-y-4">
                                        <p class="text-[11px] font-bold text-slate-400 leading-relaxed">{{ $savedAddress->address }}, {{ $savedAddress->city }}{{ $savedAddress->postal_code ? ', '.$savedAddress->postal_code : '' }}</p>
                                        <p class="text-[10px] font-black text-slate-900 dark:text-white tracking-widest">{{ $savedAddress->phone }}</p>
                                    </div>
                                    <div class="mt-8 pt-8 border-t border-slate-50 dark:border-white/5 flex gap-4">
                                        @unless($savedAddress->is_default)
                                            <button type="button" wire:click="setDefaultAddress({{ $savedAddress->id }})" class="text-[9px] font-black uppercase tracking-widest text-[var(--primary)] hover:underline">Elevate to Primary</button>
                                        @endunless
                                        <button type="button" wire:click="deleteAddress({{ $savedAddress->id }})" class="text-[9px] font-black uppercase tracking-widest text-rose-500 hover:underline">Terminate</button>
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-full py-20 text-center glass rounded-[2.5rem]">
                                    <i class="fas fa-map-marked-alt text-4xl text-slate-200 mb-6"></i>
                                    <p class="text-xs font-black uppercase tracking-widest text-slate-400">No Distribution Nodes Identified</p>
                                </div>
                            @endforelse
                        </div>
                    </section>
                @endif

                @if($activeTab === 'orders')
                    <section class="space-y-8">
                        <div class="mb-10">
                            <p class="{{ $muted }} mb-3">Transaction Registry</p>
                            <h2 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Acquisition Ledger</h2>
                        </div>
                        <div class="space-y-4">
                            @forelse($orders as $order)
                                <div class="{{ $panel }} !p-8 group hover:border-[var(--primary)] transition-all">
                                    <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                                        <div class="flex items-center gap-6">
                                            <div class="h-14 w-14 flex items-center justify-center rounded-2xl bg-slate-50 text-slate-400 dark:bg-white/5"><i class="fas fa-file-invoice text-lg"></i></div>
                                            <div>
                                                <p class="text-sm font-black text-slate-900 dark:text-white tracking-widest">#{{ $order->order_number ?? $order->id }}</p>
                                                <p class="mt-1 text-[9px] font-black text-slate-400 uppercase tracking-widest">{{ $order->created_at->format('M d, Y | h:i A') }}</p>
                                            </div>
                                        </div>
                                        <div class="flex flex-wrap items-center gap-4">
                                            <span class="rounded-full bg-slate-100 px-4 py-2 text-[9px] font-black uppercase tracking-widest text-slate-600 dark:bg-white/5 dark:text-slate-400">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span>
                                            <span class="rounded-full bg-slate-100 px-4 py-2 text-[9px] font-black uppercase tracking-widest text-slate-600 dark:bg-white/5 dark:text-slate-400">{{ ucfirst($order->payment_status ?? 'Unpaid') }}</span>
                                            <span class="text-base font-black text-slate-900 dark:text-white ml-4">Rs {{ number_format($order->total ?? 0, 2) }}</span>
                                            <a wire:navigate href="{{ route('orders.show', $order) }}" class="h-12 px-8 flex items-center justify-center rounded-full bg-slate-900 text-[9px] font-black uppercase tracking-widest text-white shadow-xl hover:scale-105 transition-all">Details</a>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="py-20 text-center glass rounded-[2.5rem]">
                                    <i class="fas fa-box-open text-4xl text-slate-200 mb-6"></i>
                                    <p class="text-xs font-black uppercase tracking-widest text-slate-400">Zero Transactional Records</p>
                                </div>
                            @endforelse
                        </div>
                    </section>
                @endif

                @if($activeTab === 'wishlist')
                    <section>
                        <div class="mb-10">
                            <p class="{{ $muted }} mb-3">Registry Reserves</p>
                            <h2 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Saved Acquisitions</h2>
                        </div>
                        <div class="grid gap-8 md:grid-cols-2 xl:grid-cols-3">
                            @forelse($wishlistProducts as $product)
                                <article class="{{ $panel }} group hover:border-[var(--primary)] transition-all">
                                    <div class="mb-6 flex items-center justify-between">
                                        <p class="text-[9px] font-black uppercase tracking-widest text-slate-400">{{ $product->brand?->name ?? 'Premium Registry' }}</p>
                                        <div class="h-8 w-8 flex items-center justify-center rounded-full bg-slate-50 text-rose-500 dark:bg-white/5"><i class="fas fa-heart text-[10px]"></i></div>
                                    </div>
                                    <h3 class="text-sm font-black text-slate-900 dark:text-white tracking-tight leading-relaxed mb-4 line-clamp-2">{{ $product->name }}</h3>
                                    <p class="text-lg font-black text-slate-900 dark:text-white mb-8">Rs {{ number_format($product->selling_price, 2) }}</p>
                                    <a wire:navigate href="{{ url('/products/'.$product->id) }}" class="w-full h-12 flex items-center justify-center rounded-full bg-slate-900 text-[9px] font-black uppercase tracking-widest text-white shadow-lg hover:scale-[1.02] transition-all">Record Entry</a>
                                </article>
                            @empty
                                <div class="col-span-full py-20 text-center glass rounded-[2.5rem]">
                                    <i class="fas fa-folder-open text-4xl text-slate-200 mb-6"></i>
                                    <p class="text-xs font-black uppercase tracking-widest text-slate-400">Registry Reserves Empty</p>
                                </div>
                            @endforelse
                        </div>
                    </section>
                @endif

                @if($activeTab === 'reviews')
                    <section class="space-y-12">
                        <div class="flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
                            <div>
                                <p class="{{ $muted }} mb-3">Audit Protocol</p>
                                <h2 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Public Verification History</h2>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                @foreach(['all' => 'Full Ledger', 'approved' => 'Verified', 'pending' => 'Pending Audit'] as $filterKey => $label)
                                    <button type="button" wire:click="$set('reviewFilter', '{{ $filterKey }}')" 
                                            class="h-10 px-6 rounded-full text-[9px] font-black uppercase tracking-widest transition-all {{ $reviewFilter === $filterKey ? 'bg-slate-900 text-white shadow-xl' : 'bg-slate-50 text-slate-400 hover:text-slate-900' }}">
                                        {{ $label }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <div class="space-y-6">
                            @forelse($reviews as $review)
                                <div class="{{ $panel }} group hover:border-[var(--primary)] transition-all">
                                    <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between mb-8">
                                        <div class="flex items-center gap-4">
                                            <div class="h-12 w-12 flex items-center justify-center rounded-2xl bg-slate-50 text-slate-400 dark:bg-white/5"><i class="fas fa-star text-xs text-amber-400"></i></div>
                                            <div>
                                                <p class="text-sm font-black text-slate-900 dark:text-white tracking-widest">{{ $review->stock?->name ?? 'Registry Item Audit' }}</p>
                                                <p class="mt-1 text-[9px] font-black text-slate-400 uppercase tracking-widest">{{ $review->title ?: 'Documentation Unlabeled' }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-6">
                                            <div class="flex items-center gap-1 text-amber-400">
                                                @for($i=0; $i<$review->rating; $i++)<i class="fas fa-star text-[10px]"></i>@endfor
                                            </div>
                                            <div class="flex gap-4">
                                                <button type="button" wire:click="editReview({{ $review->id }})" class="text-[9px] font-black uppercase tracking-widest text-[var(--primary)] hover:underline">Reconfigure</button>
                                                <button type="button" wire:click="deleteReview({{ $review->id }})" class="text-[9px] font-black uppercase tracking-widest text-rose-500 hover:underline">Purge</button>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-[11px] font-bold text-slate-500 leading-relaxed bg-slate-50/50 p-6 rounded-3xl dark:bg-white/5">{{ $review->body }}</p>
                                </div>
                            @empty
                                <div class="py-20 text-center glass rounded-[2.5rem]">
                                    <i class="fas fa-clipboard-check text-4xl text-slate-200 mb-6"></i>
                                    <p class="text-xs font-black uppercase tracking-widest text-slate-400">Audit Registry Empty</p>
                                </div>
                            @endforelse
                        </div>
                    </section>
                @endif

                @if($activeTab === 'settings')
                    <section class="space-y-12">
                        <div class="mb-10">
                            <p class="{{ $muted }} mb-3">Configuration Matrix</p>
                            <h2 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Identity Parameters</h2>
                        </div>
                        <div class="grid gap-12 lg:grid-cols-2">
                            <div class="{{ $panel }}">
                                <h3 class="text-xl font-black text-slate-900 dark:text-white mb-10">Core Matrix</h3>
                                <div class="space-y-8">
                                    <div class="space-y-2">
                                        <label class="{{ $muted }}">Entity Identifier</label>
                                        <input wire:model="name" type="text" class="{{ $input }}">
                                        @error('name')<p class="mt-1 text-[9px] font-black uppercase text-rose-500">{{ $message }}</p>@enderror
                                    </div>
                                    <div class="space-y-2">
                                        <label class="{{ $muted }}">Communication Channel (Email)</label>
                                        <input wire:model="email" type="email" class="{{ $input }}">
                                        @error('email')<p class="mt-1 text-[9px] font-black uppercase text-rose-500">{{ $message }}</p>@enderror
                                    </div>
                                    <div class="space-y-2">
                                        <label class="{{ $muted }}">Direct Line (Phone)</label>
                                        <input wire:model="phone" type="text" class="{{ $input }}">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="{{ $muted }}">Registry Date (Birthday)</label>
                                        <input wire:model="dob" type="date" class="{{ $input }}">
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-8">
                                <div class="{{ $panel }}">
                                    <h3 class="text-xl font-black text-slate-900 dark:text-white mb-10">Protocol Notifications</h3>
                                    <div class="space-y-4">
                                        @foreach(['email_offers' => 'Electronic Mail Protocols', 'sms_alerts' => 'Cellular Transmission Alerts', 'order_updates' => 'Ledger Execution Updates'] as $property => $label)
                                            <label class="flex items-center gap-4 p-5 rounded-3xl bg-slate-50/50 hover:bg-slate-50 transition-all cursor-pointer dark:bg-white/5">
                                                <input wire:model="{{ $property }}" type="checkbox" class="w-5 h-5 rounded-lg border-slate-200 text-slate-900 focus:ring-0">
                                                <span class="text-[10px] font-black uppercase tracking-widest text-slate-500">{{ $label }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                                <button type="button" wire:click="saveProfile" class="w-full h-16 rounded-full bg-[var(--primary)] text-[10px] font-black uppercase tracking-widest text-white shadow-2xl hover:scale-[1.02] transition-all">Save Identity Configuration</button>
                            </div>
                        </div>
                    </section>
                @endif

                @if($activeTab === 'security')
                    <section class="space-y-12">
                        <div class="mb-10">
                            <p class="{{ $muted }} mb-3">Defensive Protocol</p>
                            <h2 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Account Firewall</h2>
                        </div>
                        <div class="grid gap-12 xl:grid-cols-[1.1fr_0.9fr]">
                            <div class="{{ $panel }}">
                                <div class="flex flex-col gap-6 sm:flex-row sm:items-start sm:justify-between mb-10">
                                    <div>
                                        <p class="{{ $muted }} mb-2">Two-Factor Authorization</p>
                                        <h3 class="text-xl font-black text-slate-900 dark:text-white leading-relaxed">Authenticator Hardware Simulation</h3>
                                    </div>
                                    @if($user->two_factor_secret && $user->two_factor_confirmed_at)
                                        <span class="rounded-full px-4 py-2 text-[9px] font-black uppercase tracking-widest text-emerald-500 bg-emerald-500/10">Active</span>
                                    @else
                                        <span class="rounded-full px-4 py-2 text-[9px] font-black uppercase tracking-widest text-slate-400 bg-slate-50 dark:bg-white/5">Inactive</span>
                                    @endif
                                </div>

                                @if(!$user->two_factor_secret)
                                    <div class="space-y-8">
                                        <div class="space-y-2">
                                            <label class="{{ $muted }}">Identity Confirmation (Password)</label>
                                            <input wire:model="two_factor_password" type="password" class="{{ $input }}" placeholder="Confirm password to initialize 2FA">
                                            @error('two_factor_password')<p class="mt-1 text-[9px] font-black uppercase text-rose-500">{{ $message }}</p>@enderror
                                        </div>
                                        <button type="button" wire:click="enableTwoFactor" class="w-full h-14 rounded-full bg-slate-900 text-[10px] font-black uppercase tracking-widest text-white shadow-xl hover:scale-[1.02] transition-all">Initialize Firewall</button>
                                    </div>
                                @else
                                    <div class="grid gap-10 lg:grid-cols-[200px_minmax(0,1fr)]">
                                        <div class="premium-card !p-4 !rounded-3xl flex justify-center items-center bg-white">
                                            {!! $user->twoFactorQrCodeSvg() !!}
                                        </div>
                                        <div class="space-y-6">
                                            <div class="p-6 rounded-3xl bg-slate-50/50 dark:bg-white/5">
                                                <p class="text-[10px] font-black uppercase tracking-widest text-slate-900 dark:text-white mb-4">Deployment Steps</p>
                                                <ul class="space-y-3 text-[11px] font-bold text-slate-500">
                                                    <li>1. Scan the synchronized matrix code.</li>
                                                    <li>2. Transmit the 6-digit confirmation key.</li>
                                                    <li>3. Archive the fallback recovery tokens.</li>
                                                </ul>
                                            </div>
                                            @if(!$user->two_factor_confirmed_at)
                                                <div class="space-y-4">
                                                    <input wire:model="two_factor_code" type="text" class="{{ $input }}" placeholder="6-Digit Matrix Key">
                                                    @error('two_factor_code')<p class="mt-1 text-[9px] font-black uppercase text-rose-500">{{ $message }}</p>@enderror
                                                    <div class="flex gap-4">
                                                        <button type="button" wire:click="confirmTwoFactor" class="flex-1 h-12 rounded-full bg-[var(--primary)] text-[9px] font-black uppercase tracking-widest text-white shadow-xl">Confirm</button>
                                                        <button type="button" wire:click="disableTwoFactor" class="flex-1 h-12 rounded-full bg-slate-50 text-[9px] font-black uppercase tracking-widest text-slate-400">Abort</button>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="space-y-8">
                                <div class="{{ $panel }}">
                                    <p class="{{ $muted }} mb-2">Access Control</p>
                                    <h3 class="text-xl font-black text-slate-900 dark:text-white mb-10">Credential Modification</h3>
                                    <div class="space-y-6">
                                        <div class="space-y-2">
                                            <label class="{{ $muted }}">Active Password</label>
                                            <input wire:model="current_password" type="password" class="{{ $input }}">
                                        </div>
                                        <div class="space-y-2">
                                            <label class="{{ $muted }}">New Protocol Key</label>
                                            <input wire:model="password" type="password" class="{{ $input }}">
                                        </div>
                                        <button type="button" wire:click="updatePassword" class="w-full h-14 rounded-full bg-slate-900 text-[10px] font-black uppercase tracking-widest text-white shadow-xl hover:scale-[1.02] transition-all">Update Access Keys</button>
                                    </div>
                                </div>

                                <div class="{{ $panel }} !border-rose-500/20 bg-rose-50/10">
                                    <p class="text-[9px] font-black uppercase tracking-widest text-rose-500 mb-4">Critical Override</p>
                                    <h3 class="text-sm font-black text-slate-900 dark:text-white mb-6">Terminate External Sessions</h3>
                                    <div class="space-y-4">
                                        <input wire:model="logoutPassword" type="password" class="{{ $input }} !border-rose-200" placeholder="Confirm password to purge">
                                        <button type="button" wire:click="logoutOtherDevices" class="w-full h-12 rounded-full bg-rose-500 text-[9px] font-black uppercase tracking-widest text-white shadow-lg">Execute Purge</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                @endif
            </div>
        </section>
    </div>
</div>

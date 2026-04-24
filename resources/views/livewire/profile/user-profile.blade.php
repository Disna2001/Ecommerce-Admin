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
            'overview' => ['label' => 'Overview', 'icon' => 'fa-user'],
            'orders' => ['label' => 'Orders', 'icon' => 'fa-bag-shopping'],
            'addresses' => ['label' => 'Address Book', 'icon' => 'fa-location-dot'],
            'wishlist' => ['label' => 'Wishlist', 'icon' => 'fa-heart'],
            'reviews' => ['label' => 'Reviews', 'icon' => 'fa-star'],
            'settings' => ['label' => 'Settings', 'icon' => 'fa-sliders'],
            'security' => ['label' => 'Security', 'icon' => 'fa-shield-halved'],
        ];
        $panel = 'rounded-[1.75rem] border border-slate-200 bg-white p-5 shadow-[0_10px_35px_rgba(15,23,42,0.05)] dark:border-slate-800 dark:bg-slate-900/92 dark:shadow-[0_12px_40px_rgba(0,0,0,0.35)]';
        $input = 'mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-violet-400 focus:ring-4 focus:ring-violet-100 dark:border-slate-700 dark:bg-slate-950 dark:text-white dark:placeholder:text-slate-500 dark:focus:border-violet-500 dark:focus:ring-violet-500/20';
        $muted = 'text-slate-500 dark:text-slate-400';
        $defaultAddress = $addresses->firstWhere('is_default', true) ?? $addresses->first();
    @endphp

    <div class="mx-auto max-w-7xl px-4 py-8 sm:py-10">
        <section class="overflow-hidden rounded-[2.25rem] border border-white/60 bg-[linear-gradient(180deg,rgba(255,255,255,0.96),rgba(246,244,255,0.98))] shadow-[0_40px_120px_rgba(15,23,42,0.08)] backdrop-blur dark:border-slate-800 dark:bg-[linear-gradient(180deg,rgba(15,23,42,0.95),rgba(17,24,39,0.98))]">
            <div class="border-b border-slate-200/80 px-5 py-6 dark:border-slate-800 sm:px-7 lg:px-8 lg:py-7">
                <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_320px] xl:items-center">
                    <div class="flex flex-col gap-5 sm:flex-row sm:items-center">
                        <div class="relative h-24 w-24 flex-shrink-0 overflow-hidden rounded-[2rem] border border-slate-200 bg-[linear-gradient(135deg,#ede9fe,#dbeafe)] dark:border-slate-700 dark:bg-[linear-gradient(135deg,rgba(124,58,237,0.22),rgba(14,165,233,0.12))]">
                            @if($profile_photo)
                                <img src="{{ $profile_photo->temporaryUrl() }}" alt="{{ $user->name }}" class="h-full w-full object-cover">
                            @elseif($user->profile_photo_path ?? null)
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($user->profile_photo_path) }}" alt="{{ $user->name }}" class="h-full w-full object-cover">
                            @else
                                <div class="flex h-full w-full items-center justify-center text-4xl font-black text-slate-900 dark:text-white">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                            @endif
                        </div>

                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-black uppercase tracking-[0.34em] text-violet-500">Account Center</p>
                            <h1 class="mt-2 text-3xl font-black text-slate-950 dark:text-white sm:text-4xl">{{ $user->name }}</h1>
                            <p class="mt-2 truncate text-sm {{ $muted }}">{{ $user->email }}</p>
                            <div class="mt-4 flex flex-wrap gap-2">
                                <span class="rounded-full bg-slate-100 px-3 py-1.5 text-xs font-black text-slate-600 dark:bg-slate-900 dark:text-slate-200">{{ $orders->count() }} orders</span>
                                <span class="rounded-full bg-slate-100 px-3 py-1.5 text-xs font-black text-slate-600 dark:bg-slate-900 dark:text-slate-200">{{ $wishlistProducts->count() }} wishlist</span>
                                <span class="rounded-full bg-slate-100 px-3 py-1.5 text-xs font-black text-slate-600 dark:bg-slate-900 dark:text-slate-200">{{ $addresses->count() }} addresses</span>
                                @can('view-admin-menu')
                                    <span class="rounded-full bg-violet-100 px-3 py-1.5 text-xs font-black text-violet-700 dark:bg-violet-500/10 dark:text-violet-200">Admin enabled</span>
                                @endcan
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-1">
                        <div class="rounded-[1.5rem] border border-slate-200 bg-white/80 px-4 py-4 dark:border-slate-800 dark:bg-slate-800/90">
                            <p class="text-xs font-black uppercase tracking-[0.22em] {{ $muted }}">Default Address</p>
                            <p class="mt-2 text-sm font-bold text-slate-950 dark:text-white">{{ $defaultAddress?->city ?? 'Not added yet' }}</p>
                            <p class="mt-1 text-xs leading-6 {{ $muted }}">{{ $defaultAddress?->address ?? 'Add an address for faster checkout.' }}</p>
                        </div>
                        <div class="flex flex-wrap items-center gap-3 xl:justify-end">
                            <label class="inline-flex cursor-pointer items-center rounded-full border border-slate-200 bg-white px-5 py-3 text-sm font-black text-slate-900 shadow-sm dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                                Change Photo
                                <input type="file" wire:model="profile_photo" accept="image/*" class="hidden">
                            </label>
                            @if($profile_photo)
                                <button type="button" wire:click="savePhoto" class="rounded-full bg-violet-600 px-5 py-3 text-sm font-black text-white">Save</button>
                                <button type="button" wire:click="$set('profile_photo', null)" class="rounded-full bg-slate-100 px-5 py-3 text-sm font-black text-slate-700 dark:bg-slate-800 dark:text-slate-200">Cancel</button>
                            @endif
                            @can('view-admin-menu')
                                <a wire:navigate href="{{ route('admin.dashboard') }}" class="rounded-full bg-slate-950 px-5 py-3 text-sm font-black text-white dark:bg-white dark:text-slate-950">Admin Panel</a>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-b border-slate-200/80 px-5 py-4 dark:border-slate-800 sm:px-7 lg:px-8">
                <div class="flex flex-wrap gap-2">
                    @foreach($tabs as $key => $tab)
                        <button type="button" wire:click="setTab('{{ $key }}')"
                                class="inline-flex items-center gap-2 rounded-full border px-4 py-2.5 text-sm font-black transition {{ $activeTab === $key ? 'border-violet-500 bg-violet-600 text-white shadow-lg shadow-violet-500/20' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-100 hover:text-slate-950 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700' }}">
                            <i class="fas {{ $tab['icon'] }} text-xs"></i>
                            <span>{{ $tab['label'] }}</span>
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="px-5 py-6 sm:px-7 lg:px-8 lg:py-8">
                @if($activeTab === 'overview')
                    <div class="space-y-6">
                        <div class="grid gap-4 lg:grid-cols-3">
                            <div class="{{ $panel }}">
                                <p class="text-xs font-black uppercase tracking-[0.24em] {{ $muted }}">Contact</p>
                                <p class="mt-4 text-2xl font-black text-slate-950 dark:text-white">{{ $user->phone ?: 'Not set' }}</p>
                                <p class="mt-3 text-sm leading-6 {{ $muted }}">{{ $user->address ?: 'Add your address details so checkout stays automatic and accurate.' }}</p>
                            </div>
                            <div class="{{ $panel }}">
                                <p class="text-xs font-black uppercase tracking-[0.24em] {{ $muted }}">Latest Order</p>
                                <p class="mt-4 text-2xl font-black text-slate-950 dark:text-white">{{ optional($orders->first())->order_number ?? 'No orders yet' }}</p>
                                <p class="mt-3 text-sm leading-6 {{ $muted }}">{{ $orders->whereIn('status', ['pending','processing','shipped'])->count() }} active order(s) in progress.</p>
                            </div>
                            <div class="{{ $panel }}">
                                <p class="text-xs font-black uppercase tracking-[0.24em] {{ $muted }}">Address Book</p>
                                <p class="mt-4 text-2xl font-black text-slate-950 dark:text-white">{{ $addresses->count() }}</p>
                                <p class="mt-3 text-sm leading-6 {{ $muted }}">Manage saved addresses and choose which one should appear first in checkout.</p>
                                <button type="button" wire:click="setTab('addresses')" class="mt-4 inline-flex rounded-full bg-violet-50 px-4 py-2 text-sm font-black text-violet-700 dark:bg-violet-500/10 dark:text-violet-200">Open address book</button>
                            </div>
                        </div>

                        <div class="grid gap-6 xl:grid-cols-[1.3fr_0.9fr]">
                            <section class="{{ $panel }}">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <p class="text-xs font-black uppercase tracking-[0.24em] {{ $muted }}">Recent Orders</p>
                                        <h2 class="mt-2 text-2xl font-black text-slate-950 dark:text-white">Account activity</h2>
                                    </div>
                                    <button type="button" wire:click="setTab('orders')" class="rounded-full bg-violet-50 px-4 py-2 text-sm font-black text-violet-700 dark:bg-violet-500/10 dark:text-violet-200">View orders</button>
                                </div>
                                <div class="mt-5 space-y-3">
                                    @forelse($orders->take(4) as $order)
                                        <div class="flex flex-col gap-3 rounded-2xl border border-slate-200 p-4 dark:border-slate-700 sm:flex-row sm:items-center sm:justify-between">
                                            <div>
                                                <p class="font-black text-slate-950 dark:text-white">#{{ $order->order_number ?? $order->id }}</p>
                                                <p class="mt-1 text-xs {{ $muted }}">{{ $order->created_at->format('M d, Y') }}</p>
                                            </div>
                                            <div class="flex flex-wrap items-center gap-2">
                                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-black text-slate-600 dark:bg-slate-900 dark:text-slate-200">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span>
                                                <span class="text-sm font-black text-slate-950 dark:text-white">Rs {{ number_format($order->total ?? 0, 2) }}</span>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="rounded-2xl border border-dashed border-slate-300 px-5 py-12 text-center text-sm {{ $muted }} dark:border-white/10">No orders yet.</div>
                                    @endforelse
                                </div>
                            </section>

                            <section class="{{ $panel }}">
                                <p class="text-xs font-black uppercase tracking-[0.24em] {{ $muted }}">Quick Snapshot</p>
                                <h2 class="mt-2 text-2xl font-black text-slate-950 dark:text-white">What this account is using</h2>
                                <div class="mt-5 space-y-4">
                                        <div class="rounded-2xl bg-slate-50 p-4 dark:bg-slate-950/90">
                                        <p class="text-sm font-black text-slate-950 dark:text-white">Primary checkout address</p>
                                        <p class="mt-2 text-sm leading-6 {{ $muted }}">{{ $defaultAddress?->address ? $defaultAddress->address.', '.$defaultAddress->city : 'No default address set yet.' }}</p>
                                    </div>
                                        <div class="rounded-2xl bg-slate-50 p-4 dark:bg-slate-950/90">
                                        <p class="text-sm font-black text-slate-950 dark:text-white">Review activity</p>
                                        <p class="mt-2 text-sm leading-6 {{ $muted }}">{{ $reviews->count() }} review(s) submitted so far.</p>
                                    </div>
                                        <div class="rounded-2xl bg-slate-50 p-4 dark:bg-slate-950/90">
                                        <p class="text-sm font-black text-slate-950 dark:text-white">Saved products</p>
                                        <p class="mt-2 text-sm leading-6 {{ $muted }}">{{ $wishlistProducts->count() }} product(s) in wishlist.</p>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                @endif

                @if($activeTab === 'addresses')
                    <section class="space-y-6">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                            <div>
                                <p class="text-xs font-black uppercase tracking-[0.24em] {{ $muted }}">Address Book</p>
                                <h2 class="mt-2 text-3xl font-black text-slate-950 dark:text-white">Saved checkout addresses</h2>
                                <p class="mt-3 max-w-2xl text-sm leading-7 {{ $muted }}">Your default address will appear automatically on checkout and is the address we pass into the payment flow.</p>
                            </div>
                            <button type="button" wire:click="$toggle('showAddressForm')" class="rounded-full bg-violet-600 px-5 py-3 text-sm font-black text-white">{{ $showAddressForm ? 'Close Form' : 'Add Address' }}</button>
                        </div>

                        @if($showAddressForm)
                            <div class="{{ $panel }}">
                                <h3 class="text-xl font-black text-slate-950 dark:text-white">Add a new address</h3>
                                <div class="mt-5 grid gap-4 md:grid-cols-2">
                                    <div><label class="text-xs font-black uppercase tracking-wide {{ $muted }}">Full Name</label><input wire:model="addr_name" type="text" class="{{ $input }}">@error('addr_name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror</div>
                                    <div><label class="text-xs font-black uppercase tracking-wide {{ $muted }}">Phone</label><input wire:model="addr_phone" type="text" class="{{ $input }}">@error('addr_phone')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror</div>
                                    <div class="md:col-span-2"><label class="text-xs font-black uppercase tracking-wide {{ $muted }}">Street Address</label><input wire:model="addr_address" type="text" class="{{ $input }}">@error('addr_address')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror</div>
                                    <div><label class="text-xs font-black uppercase tracking-wide {{ $muted }}">City</label><input wire:model="addr_city" type="text" class="{{ $input }}">@error('addr_city')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror</div>
                                    <div><label class="text-xs font-black uppercase tracking-wide {{ $muted }}">Postal Code</label><input wire:model="addr_postal" type="text" class="{{ $input }}"></div>
                                </div>
                                <label class="mt-5 flex items-center gap-3 text-sm font-bold text-slate-700 dark:text-slate-200">
                                    <input wire:model="addr_is_default" type="checkbox" class="rounded border-slate-300 text-violet-600 focus:ring-violet-500">
                                    Set this as the default checkout address
                                </label>
                                <button type="button" wire:click="saveAddress" class="mt-5 rounded-full bg-slate-950 px-6 py-3 text-sm font-black text-white dark:bg-white dark:text-slate-950">Save Address</button>
                            </div>
                        @endif

                        <div class="grid gap-4 lg:grid-cols-2">
                            @forelse($addresses as $savedAddress)
                                <div class="{{ $panel }}">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <div class="flex flex-wrap items-center gap-2">
                                                <p class="text-lg font-black text-slate-950 dark:text-white">{{ $savedAddress->name }}</p>
                                                @if($savedAddress->is_default)
                                                    <span class="rounded-full bg-emerald-100 px-3 py-1 text-[10px] font-black uppercase tracking-[0.18em] text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-200">Default</span>
                                                @endif
                                            </div>
                                            <p class="mt-3 text-sm leading-7 {{ $muted }}">{{ $savedAddress->address }}, {{ $savedAddress->city }}{{ $savedAddress->postal_code ? ', '.$savedAddress->postal_code : '' }}</p>
                                            <p class="mt-2 text-sm font-black text-slate-700 dark:text-slate-200">{{ $savedAddress->phone }}</p>
                                        </div>
                                    </div>
                                    <div class="mt-5 flex flex-wrap gap-2">
                                        @unless($savedAddress->is_default)
                                            <button type="button" wire:click="setDefaultAddress({{ $savedAddress->id }})" class="rounded-full bg-violet-50 px-4 py-2 text-sm font-black text-violet-700 dark:bg-violet-500/10 dark:text-violet-200">Set Default</button>
                                        @endunless
                                        <button type="button" wire:click="deleteAddress({{ $savedAddress->id }})" class="rounded-full bg-rose-50 px-4 py-2 text-sm font-black text-rose-600 dark:bg-rose-500/10 dark:text-rose-200">Remove</button>
                                    </div>
                                </div>
                            @empty
                                <div class="rounded-[1.75rem] border border-dashed border-slate-300 bg-white px-5 py-14 text-center text-sm {{ $muted }} dark:border-white/10 dark:bg-slate-950 lg:col-span-2">No saved addresses yet. Add one here and checkout will pick it up automatically.</div>
                            @endforelse
                        </div>
                    </section>
                @endif

                @if($activeTab === 'orders')
                    <section class="space-y-4">
                        <h2 class="text-3xl font-black text-slate-950 dark:text-white">Orders</h2>
                        @forelse($orders as $order)
                            <div class="{{ $panel }}">
                                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                                    <div>
                                        <p class="font-black text-slate-950 dark:text-white">#{{ $order->order_number ?? $order->id }}</p>
                                        <p class="mt-1 text-sm {{ $muted }}">{{ $order->created_at->format('M d, Y h:i A') }}</p>
                                    </div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-black text-slate-600 dark:bg-slate-900 dark:text-slate-200">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span>
                                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-black text-slate-600 dark:bg-slate-900 dark:text-slate-200">{{ ucfirst($order->payment_status ?? 'unpaid') }}</span>
                                        <span class="text-sm font-black text-slate-950 dark:text-white">Rs {{ number_format($order->total ?? 0, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-[1.75rem] border border-dashed border-slate-300 px-5 py-14 text-center text-sm {{ $muted }} dark:border-white/10">No orders yet.</div>
                        @endforelse
                    </section>
                @endif

                @if($activeTab === 'wishlist')
                    <section>
                        <h2 class="text-3xl font-black text-slate-950 dark:text-white">Wishlist</h2>
                        <div class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                            @forelse($wishlistProducts as $product)
                                <div class="{{ $panel }}">
                                    <p class="text-xs font-black uppercase tracking-[0.18em] {{ $muted }}">{{ $product->brand?->name ?? 'Product' }}</p>
                                    <h3 class="mt-2 font-black text-slate-950 dark:text-white">{{ $product->name }}</h3>
                                    <p class="mt-2 text-sm {{ $muted }}">Rs {{ number_format($product->selling_price, 2) }}</p>
                                    <a wire:navigate href="{{ url('/products/'.$product->id) }}" class="mt-4 inline-flex rounded-full bg-violet-600 px-4 py-2 text-sm font-black text-white">View Product</a>
                                </div>
                            @empty
                                <div class="rounded-[1.75rem] border border-dashed border-slate-300 px-5 py-14 text-center text-sm {{ $muted }} dark:border-white/10 md:col-span-2 xl:col-span-3">No saved items yet.</div>
                            @endforelse
                        </div>
                    </section>
                @endif

                @if($activeTab === 'reviews')
                    <section>
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <h2 class="text-3xl font-black text-slate-950 dark:text-white">Reviews</h2>
                            <div class="flex flex-wrap gap-2">
                                @foreach(['all' => 'All', 'approved' => 'Approved', 'pending' => 'Pending'] as $filterKey => $label)
                                    <button type="button" wire:click="$set('reviewFilter', '{{ $filterKey }}')" class="rounded-full px-4 py-2 text-sm font-black {{ $reviewFilter === $filterKey ? 'bg-violet-600 text-white' : 'bg-slate-100 text-slate-600 dark:bg-slate-900 dark:text-slate-200' }}">{{ $label }}</button>
                                @endforeach
                            </div>
                        </div>

                        @if($showReviewForm)
                            <div class="{{ $panel }} mt-5">
                                <div class="grid gap-4">
                                    <input wire:model="review_rating" type="number" min="1" max="5" class="{{ $input }}" placeholder="Rating 1-5">
                                    <input wire:model="review_title" type="text" class="{{ $input }}" placeholder="Review title">
                                    <textarea wire:model="review_body" rows="4" class="{{ $input }} resize-none" placeholder="Share your experience"></textarea>
                                    <div class="flex flex-wrap gap-3">
                                        <button type="button" wire:click="saveReview" class="rounded-full bg-violet-600 px-5 py-2.5 text-sm font-black text-white">Save Review</button>
                                        <button type="button" wire:click="cancelReview" class="rounded-full bg-slate-100 px-5 py-2.5 text-sm font-black text-slate-600 dark:bg-slate-900 dark:text-slate-200">Cancel</button>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="mt-5 space-y-4">
                            @forelse($reviews as $review)
                                <div class="{{ $panel }}">
                                    <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                                        <div>
                                            <p class="font-black text-slate-950 dark:text-white">{{ $review->stock?->name ?? 'Product Review' }}</p>
                                            <p class="mt-1 text-sm {{ $muted }}">{{ $review->title ?: 'No title' }}</p>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-black text-amber-700">{{ $review->rating }}/5</span>
                                            <button type="button" wire:click="editReview({{ $review->id }})" class="text-sm font-black text-violet-600">Edit</button>
                                            <button type="button" wire:click="deleteReview({{ $review->id }})" class="text-sm font-black text-rose-500">Delete</button>
                                        </div>
                                    </div>
                                    <p class="mt-4 text-sm leading-7 {{ $muted }}">{{ $review->body }}</p>
                                </div>
                            @empty
                                <div class="rounded-[1.75rem] border border-dashed border-slate-300 px-5 py-14 text-center text-sm {{ $muted }} dark:border-white/10">No reviews yet.</div>
                            @endforelse
                        </div>
                    </section>
                @endif

                @if($activeTab === 'settings')
                    <section>
                        <h2 class="text-3xl font-black text-slate-950 dark:text-white">Profile Settings</h2>
                        <div class="mt-5 grid gap-4 lg:grid-cols-2">
                            <div class="{{ $panel }}">
                                <div class="grid gap-4">
                                    <div><label class="text-xs font-black uppercase tracking-wide {{ $muted }}">Name</label><input wire:model="name" type="text" class="{{ $input }}">@error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror</div>
                                    <div><label class="text-xs font-black uppercase tracking-wide {{ $muted }}">Email</label><input wire:model="email" type="email" class="{{ $input }}">@error('email')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror</div>
                                    <div><label class="text-xs font-black uppercase tracking-wide {{ $muted }}">Phone</label><input wire:model="phone" type="text" class="{{ $input }}"></div>
                                    <div><label class="text-xs font-black uppercase tracking-wide {{ $muted }}">Birthday</label><input wire:model="dob" type="date" class="{{ $input }}"></div>
                                    <div><label class="text-xs font-black uppercase tracking-wide {{ $muted }}">Profile Address Note</label><textarea wire:model="address" rows="4" class="{{ $input }} resize-none"></textarea></div>
                                </div>
                            </div>
                            <div class="{{ $panel }}">
                                <p class="text-lg font-black text-slate-950 dark:text-white">Notifications</p>
                                <div class="mt-4 grid gap-3">
                                    @foreach(['email_offers' => 'Email Offers', 'sms_alerts' => 'SMS Alerts', 'order_updates' => 'Order Updates'] as $property => $label)
                                        <label class="flex items-center gap-3 rounded-2xl bg-slate-50 px-4 py-3 text-sm font-bold text-slate-700 dark:bg-slate-950/90 dark:text-slate-200">
                                            <input wire:model="{{ $property }}" type="checkbox" class="rounded border-slate-300 text-violet-600 focus:ring-violet-500">
                                            {{ $label }}
                                        </label>
                                    @endforeach
                                </div>
                                <button type="button" wire:click="saveProfile" wire:loading.attr="disabled" class="mt-6 rounded-full bg-violet-600 px-6 py-3 text-sm font-black text-white">Save Profile</button>
                            </div>
                        </div>
                    </section>
                @endif

                @if($activeTab === 'security')
                    <section>
                        <h2 class="text-3xl font-black text-slate-950 dark:text-white">Security</h2>
                        <div class="mt-5 grid gap-5 xl:grid-cols-[1.15fr_0.85fr]">
                            <div class="{{ $panel }}">
                                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                    <div>
                                        <p class="text-xs font-black uppercase tracking-[0.24em] {{ $muted }}">Two-Factor Authentication</p>
                                        <h3 class="mt-2 text-2xl font-black text-slate-950 dark:text-white">Protect this account with an authenticator app</h3>
                                        <p class="mt-3 text-sm leading-7 {{ $muted }}">Use Google Authenticator, Microsoft Authenticator, 1Password, or any TOTP app to require a one-time code during sign in.</p>
                                    </div>
                                    @if($user->two_factor_secret && $user->two_factor_confirmed_at)
                                        <span class="rounded-full bg-emerald-100 px-3 py-1.5 text-xs font-black uppercase tracking-[0.18em] text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-200">Enabled</span>
                                    @elseif($user->two_factor_secret)
                                        <span class="rounded-full bg-amber-100 px-3 py-1.5 text-xs font-black uppercase tracking-[0.18em] text-amber-700 dark:bg-amber-500/10 dark:text-amber-200">Pending Confirmation</span>
                                    @else
                                        <span class="rounded-full bg-slate-100 px-3 py-1.5 text-xs font-black uppercase tracking-[0.18em] text-slate-600 dark:bg-slate-800 dark:text-slate-200">Disabled</span>
                                    @endif
                                </div>

                                @if(!$user->two_factor_secret)
                                    <div class="mt-6 space-y-4">
                                        <div>
                                            <label class="text-xs font-black uppercase tracking-wide {{ $muted }}">Current Password</label>
                                            <input wire:model="two_factor_password" type="password" class="{{ $input }}" placeholder="Confirm your password to enable 2FA">
                                            @error('two_factor_password')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                                        </div>
                                        <button type="button" wire:click="enableTwoFactor" class="rounded-full bg-violet-600 px-6 py-3 text-sm font-black text-white">Enable Two-Factor Authentication</button>
                                    </div>
                                @else
                                    <div class="mt-6 grid gap-5 lg:grid-cols-[220px_minmax(0,1fr)]">
                                        <div class="rounded-[1.5rem] border border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-slate-950">
                                            <p class="text-xs font-black uppercase tracking-[0.22em] {{ $muted }}">Scan QR</p>
                                            <div class="mt-4 flex justify-center rounded-2xl bg-white p-4">
                                                {!! $user->twoFactorQrCodeSvg() !!}
                                            </div>
                                        </div>
                                        <div class="space-y-4">
                                            <div class="rounded-[1.5rem] bg-slate-50 p-4 dark:bg-slate-950/90">
                                                <p class="text-sm font-black text-slate-950 dark:text-white">Setup steps</p>
                                                <ol class="mt-3 space-y-2 text-sm leading-6 {{ $muted }}">
                                                    <li>1. Open your authenticator app and scan the QR code.</li>
                                                    <li>2. Enter the 6-digit code below to finish setup.</li>
                                                    <li>3. Save the recovery codes somewhere safe.</li>
                                                </ol>
                                            </div>

                                            @if(!$user->two_factor_confirmed_at)
                                                <div>
                                                    <label class="text-xs font-black uppercase tracking-wide {{ $muted }}">Authenticator Code</label>
                                                    <input wire:model="two_factor_code" type="text" inputmode="numeric" class="{{ $input }}" placeholder="Enter the 6-digit code">
                                                    @error('two_factor_code')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                                                    <div class="mt-4 flex flex-wrap gap-3">
                                                        <button type="button" wire:click="confirmTwoFactor" class="rounded-full bg-violet-600 px-5 py-3 text-sm font-black text-white">Confirm Two-Factor</button>
                                                        <button type="button" wire:click="disableTwoFactor" class="rounded-full bg-slate-100 px-5 py-3 text-sm font-black text-slate-700 dark:bg-slate-800 dark:text-slate-200">Cancel Setup</button>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="rounded-[1.5rem] border border-emerald-200 bg-emerald-50 p-4 dark:border-emerald-500/20 dark:bg-emerald-500/10">
                                                    <p class="text-sm font-black text-emerald-800 dark:text-emerald-200">Two-factor authentication is active</p>
                                                    <p class="mt-2 text-sm leading-6 text-emerald-700/90 dark:text-emerald-200/80">You will be asked for a time-based code from your authenticator app during sign in.</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    @if($user->two_factor_confirmed_at)
                                        <div class="mt-5 rounded-[1.5rem] border border-slate-200 bg-white p-5 dark:border-slate-700 dark:bg-slate-950">
                                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                                <div>
                                                    <p class="text-xs font-black uppercase tracking-[0.22em] {{ $muted }}">Recovery Codes</p>
                                                    <p class="mt-2 text-sm leading-6 {{ $muted }}">Use these one-time codes if you lose access to your authenticator app.</p>
                                                </div>
                                                <button type="button" wire:click="regenerateTwoFactorRecoveryCodes" class="rounded-full bg-slate-100 px-4 py-2 text-sm font-black text-slate-700 dark:bg-slate-800 dark:text-slate-200">Regenerate Codes</button>
                                            </div>
                                            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                                                @foreach($user->recoveryCodes() as $code)
                                                    <div class="rounded-2xl bg-slate-50 px-4 py-3 font-mono text-sm text-slate-700 dark:bg-slate-900 dark:text-slate-200">{{ $code }}</div>
                                                @endforeach
                                            </div>
                                        </div>

                                        <div class="mt-5 rounded-[1.5rem] border border-rose-200 bg-rose-50 p-5 dark:border-rose-500/20 dark:bg-rose-500/10">
                                            <p class="text-sm font-black text-rose-700 dark:text-rose-200">Disable two-factor authentication</p>
                                            <p class="mt-2 text-sm leading-6 text-rose-700/90 dark:text-rose-200/80">Enter your current password if you want to remove authenticator protection from this account.</p>
                                            <div class="mt-4 flex flex-col gap-3 md:flex-row">
                                                <input wire:model="two_factor_password" type="password" class="{{ $input }} md:mt-0" placeholder="Current password">
                                                <button type="button" wire:click="disableTwoFactor" class="rounded-full bg-rose-600 px-5 py-3 text-sm font-black text-white">Disable 2FA</button>
                                            </div>
                                            @error('two_factor_password')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                                        </div>
                                    @endif
                                @endif
                            </div>

                            <div class="{{ $panel }}">
                                <p class="text-xs font-black uppercase tracking-[0.24em] {{ $muted }}">Session Security</p>
                                <h3 class="mt-2 text-2xl font-black text-slate-950 dark:text-white">Password and device controls</h3>
                                <p class="mt-3 text-sm leading-7 {{ $muted }}">Keep your password fresh and sign out old sessions if you think another device still has access.</p>

                                <div class="mt-6 grid gap-4">
                                    <div><label class="text-xs font-black uppercase tracking-wide {{ $muted }}">Current Password</label><input wire:model="current_password" type="{{ $showCurrentPw ? 'text' : 'password' }}" class="{{ $input }}">@error('current_password')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror</div>
                                    <div><label class="text-xs font-black uppercase tracking-wide {{ $muted }}">New Password</label><input wire:model="password" type="{{ $showNewPw ? 'text' : 'password' }}" class="{{ $input }}">@error('password')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror</div>
                                    <div><label class="text-xs font-black uppercase tracking-wide {{ $muted }}">Confirm Password</label><input wire:model="password_confirmation" type="password" class="{{ $input }}"></div>
                                </div>

                                <div class="mt-6 flex flex-wrap gap-3">
                                    <button type="button" wire:click="$toggle('showCurrentPw')" class="rounded-full bg-slate-100 px-4 py-2 text-sm font-black text-slate-600 dark:bg-slate-800 dark:text-slate-200">Show Current</button>
                                    <button type="button" wire:click="$toggle('showNewPw')" class="rounded-full bg-slate-100 px-4 py-2 text-sm font-black text-slate-600 dark:bg-slate-800 dark:text-slate-200">Show New</button>
                                    <button type="button" wire:click="updatePassword" class="rounded-full bg-violet-600 px-6 py-3 text-sm font-black text-white">Update Password</button>
                                </div>

                                <div class="mt-8 rounded-[1.5rem] border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-950/90">
                                    <p class="text-sm font-black text-slate-950 dark:text-white">Active sessions</p>
                                    <p class="mt-2 text-sm leading-6 {{ $muted }}">Current device: {{ request()->ip() }}</p>
                                    <div class="mt-4 flex flex-col gap-3">
                                        <input wire:model="logoutPassword" type="password" class="{{ $input }} md:mt-0" placeholder="Confirm password to sign out other devices">
                                        <button type="button" wire:click="logoutOtherDevices" class="rounded-full bg-slate-950 px-5 py-3 text-sm font-black text-white dark:bg-white dark:text-slate-950">Sign Out Other Devices</button>
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

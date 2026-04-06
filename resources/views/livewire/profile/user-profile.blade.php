<div class="relative">
    <div
        x-data="{ show:false, message:'', type:'success' }"
        x-on:notify.window="show=true; message=$event.detail.message; type=$event.detail.type; setTimeout(()=>show=false,3200)"
        x-show="show"
        x-transition
        class="fixed bottom-5 right-5 z-[90] flex items-center gap-2 rounded-2xl px-5 py-3 text-sm font-semibold text-white shadow-xl"
        :class="type==='success' ? 'bg-emerald-500' : (type==='error' ? 'bg-red-500' : 'bg-violet-500')"
        style="display:none"
    >
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
            'settings' => ['label' => 'Settings', 'icon' => 'fa-user-gear'],
            'security' => ['label' => 'Security', 'icon' => 'fa-shield-halved'],
        ];
        $panel = 'rounded-[1.5rem] border border-slate-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-slate-950/85';
        $input = 'w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-violet-400 focus:ring-4 focus:ring-violet-100 dark:border-white/10 dark:bg-slate-900 dark:text-white dark:focus:ring-violet-500/20';
        $muted = 'text-slate-500 dark:text-slate-400';
    @endphp

    <div class="mx-auto max-w-7xl px-4 py-8 sm:py-10">
        <section class="overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-[0_28px_90px_rgba(15,23,42,0.08)] dark:border-white/10 dark:bg-slate-950">
            <div class="relative isolate overflow-hidden bg-slate-950 px-5 py-8 text-white sm:px-8 lg:px-10">
                <div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_top_left,rgba(124,58,237,0.45),transparent_32%),radial-gradient(circle_at_bottom_right,rgba(14,165,233,0.32),transparent_36%)]"></div>
                <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                    <div class="flex flex-col gap-5 sm:flex-row sm:items-center">
                        <div class="relative h-28 w-28 flex-shrink-0 overflow-hidden rounded-[2rem] border border-white/20 bg-white/10">
                            @if($profile_photo)
                                <img src="{{ $profile_photo->temporaryUrl() }}" alt="{{ $user->name }}" class="h-full w-full object-cover">
                            @elseif($user->profile_photo_path ?? null)
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($user->profile_photo_path) }}" alt="{{ $user->name }}" class="h-full w-full object-cover">
                            @else
                                <div class="flex h-full w-full items-center justify-center text-5xl font-black">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                            @endif
                        </div>

                        <div class="min-w-0">
                            <p class="text-xs font-bold uppercase tracking-[0.32em] text-violet-200">Customer Profile</p>
                            <h1 class="mt-3 truncate text-3xl font-black sm:text-4xl">{{ $user->name }}</h1>
                            <p class="mt-2 truncate text-sm text-slate-300">{{ $user->email }}</p>
                            <div class="mt-4 flex flex-wrap gap-2">
                                <span class="rounded-full bg-white/10 px-3 py-1.5 text-xs font-bold">{{ $orders->count() }} orders</span>
                                <span class="rounded-full bg-white/10 px-3 py-1.5 text-xs font-bold">{{ $wishlistProducts->count() }} saved</span>
                                <span class="rounded-full bg-white/10 px-3 py-1.5 text-xs font-bold">{{ $addresses->count() }} addresses</span>
                                @can('view-admin-menu')
                                    <span class="rounded-full bg-violet-500 px-3 py-1.5 text-xs font-bold">Admin access</span>
                                @endcan
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <label class="cursor-pointer rounded-full bg-white px-5 py-3 text-sm font-black text-slate-950 shadow-sm">
                            Change Photo
                            <input type="file" wire:model="profile_photo" accept="image/*" class="hidden">
                        </label>
                        @if($profile_photo)
                            <button wire:click="savePhoto" class="rounded-full bg-violet-500 px-5 py-3 text-sm font-black text-white">Save Photo</button>
                            <button wire:click="$set('profile_photo', null)" class="rounded-full bg-white/10 px-5 py-3 text-sm font-black text-white">Cancel</button>
                        @endif
                        @can('view-admin-menu')
                            <a wire:navigate href="{{ route('admin.dashboard') }}" class="rounded-full bg-white/10 px-5 py-3 text-sm font-black text-white">Admin Panel</a>
                        @endcan
                    </div>
                </div>
            </div>

            <div class="grid min-h-[640px] lg:grid-cols-[280px_minmax(0,1fr)]">
                <aside class="border-b border-slate-200 bg-slate-50/80 p-4 dark:border-white/10 dark:bg-slate-900/50 lg:border-b-0 lg:border-r">
                    <div class="sticky top-28 space-y-2">
                        @foreach($tabs as $key => $tab)
                            <button
                                type="button"
                                wire:click="setTab('{{ $key }}')"
                                class="flex w-full items-center gap-3 rounded-2xl px-4 py-3 text-left text-sm font-bold transition {{ $activeTab === $key ? 'bg-violet-600 text-white shadow-lg shadow-violet-500/20' : 'text-slate-600 hover:bg-white hover:text-slate-950 dark:text-slate-300 dark:hover:bg-slate-950 dark:hover:text-white' }}"
                            >
                                <i class="fas {{ $tab['icon'] }} w-5 text-center"></i>
                                {{ $tab['label'] }}
                            </button>
                        @endforeach
                    </div>
                </aside>

                <main class="min-w-0 bg-white p-5 dark:bg-slate-950 sm:p-7 lg:p-8">
                    @if($activeTab === 'overview')
                        <div class="space-y-6">
                            <div class="grid gap-4 md:grid-cols-3">
                                <div class="{{ $panel }}">
                                    <p class="text-xs font-bold uppercase tracking-[0.24em] {{ $muted }}">Contact</p>
                                    <p class="mt-4 text-lg font-black text-slate-950 dark:text-white">{{ $user->phone ?: 'Phone not set' }}</p>
                                    <p class="mt-2 text-sm leading-6 {{ $muted }}">{{ $user->address ?: 'Address not set' }}</p>
                                </div>
                                <div class="{{ $panel }}">
                                    <p class="text-xs font-bold uppercase tracking-[0.24em] {{ $muted }}">Latest Order</p>
                                    <p class="mt-4 text-lg font-black text-slate-950 dark:text-white">{{ optional($orders->first())->order_number ?? 'No orders yet' }}</p>
                                    <p class="mt-2 text-sm {{ $muted }}">{{ $orders->whereIn('status', ['pending','processing','shipped'])->count() }} active order(s)</p>
                                </div>
                                <div class="{{ $panel }}">
                                    <p class="text-xs font-bold uppercase tracking-[0.24em] {{ $muted }}">Saved Address</p>
                                    <p class="mt-4 text-lg font-black text-slate-950 dark:text-white">{{ $addresses->first()?->city ?? 'Not added' }}</p>
                                    <button type="button" wire:click="setTab('addresses')" class="mt-3 text-sm font-black text-violet-600">Manage address book</button>
                                </div>
                            </div>

                            <section class="{{ $panel }}">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <p class="text-xs font-bold uppercase tracking-[0.24em] {{ $muted }}">Recent Orders</p>
                                        <h2 class="mt-2 text-2xl font-black text-slate-950 dark:text-white">Account activity</h2>
                                    </div>
                                    <button type="button" wire:click="setTab('orders')" class="rounded-full bg-violet-50 px-4 py-2 text-sm font-black text-violet-700 dark:bg-violet-500/10 dark:text-violet-200">View orders</button>
                                </div>
                                <div class="mt-5 space-y-3">
                                    @forelse($orders->take(4) as $order)
                                        <div class="flex flex-col gap-3 rounded-2xl border border-slate-200 px-4 py-4 dark:border-white/10 sm:flex-row sm:items-center sm:justify-between">
                                            <div>
                                                <p class="font-black text-slate-950 dark:text-white">#{{ $order->order_number ?? $order->id }}</p>
                                                <p class="mt-1 text-xs {{ $muted }}">{{ $order->created_at->format('M d, Y') }}</p>
                                            </div>
                                            <div class="flex flex-wrap items-center gap-2">
                                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600 dark:bg-slate-900 dark:text-slate-200">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span>
                                                <span class="text-sm font-black text-slate-950 dark:text-white">Rs {{ number_format($order->total ?? 0, 2) }}</span>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="rounded-2xl border border-dashed border-slate-300 px-5 py-12 text-center text-sm {{ $muted }} dark:border-white/10">No orders yet.</div>
                                    @endforelse
                                </div>
                            </section>
                        </div>
                    @endif

                    @if($activeTab === 'addresses')
                        <section class="space-y-6">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-[0.24em] {{ $muted }}">Address Book</p>
                                    <h2 class="mt-2 text-2xl font-black text-slate-950 dark:text-white">Saved checkout addresses</h2>
                                    <p class="mt-2 text-sm {{ $muted }}">Default addresses appear automatically on checkout and are sent to PayHere with the order.</p>
                                </div>
                                <button type="button" wire:click="$toggle('showAddressForm')" class="rounded-full bg-violet-600 px-5 py-3 text-sm font-black text-white">
                                    {{ $showAddressForm ? 'Close Form' : 'Add Address' }}
                                </button>
                            </div>

                            @if($showAddressForm)
                                <div class="{{ $panel }}">
                                    <h3 class="text-lg font-black text-slate-950 dark:text-white">New address</h3>
                                    <div class="mt-5 grid gap-4 md:grid-cols-2">
                                        <div><label class="text-xs font-bold uppercase tracking-wide {{ $muted }}">Full Name</label><input wire:model="addr_name" type="text" class="{{ $input }} mt-2">@error('addr_name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror</div>
                                        <div><label class="text-xs font-bold uppercase tracking-wide {{ $muted }}">Phone</label><input wire:model="addr_phone" type="text" class="{{ $input }} mt-2">@error('addr_phone')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror</div>
                                        <div class="md:col-span-2"><label class="text-xs font-bold uppercase tracking-wide {{ $muted }}">Street Address</label><input wire:model="addr_address" type="text" class="{{ $input }} mt-2">@error('addr_address')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror</div>
                                        <div><label class="text-xs font-bold uppercase tracking-wide {{ $muted }}">City</label><input wire:model="addr_city" type="text" class="{{ $input }} mt-2">@error('addr_city')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror</div>
                                        <div><label class="text-xs font-bold uppercase tracking-wide {{ $muted }}">Postal Code</label><input wire:model="addr_postal" type="text" class="{{ $input }} mt-2"></div>
                                    </div>
                                    <label class="mt-5 flex items-center gap-3 text-sm font-bold text-slate-700 dark:text-slate-200">
                                        <input wire:model="addr_is_default" type="checkbox" class="rounded border-slate-300 text-violet-600 focus:ring-violet-500">
                                        Use as default checkout address
                                    </label>
                                    <button type="button" wire:click="saveAddress" class="mt-5 rounded-full bg-slate-950 px-6 py-3 text-sm font-black text-white dark:bg-white dark:text-slate-950">Save Address</button>
                                </div>
                            @endif

                            <div class="grid gap-4 md:grid-cols-2">
                                @forelse($addresses as $savedAddress)
                                    <div class="{{ $panel }}">
                                        <div class="flex items-start justify-between gap-4">
                                            <div>
                                                <p class="text-lg font-black text-slate-950 dark:text-white">{{ $savedAddress->name }}</p>
                                                <p class="mt-2 text-sm leading-6 {{ $muted }}">{{ $savedAddress->address }}, {{ $savedAddress->city }}{{ $savedAddress->postal_code ? ', '.$savedAddress->postal_code : '' }}</p>
                                                <p class="mt-2 text-sm font-bold text-slate-700 dark:text-slate-200">{{ $savedAddress->phone }}</p>
                                            </div>
                                            @if($savedAddress->is_default)
                                                <span class="rounded-full bg-emerald-100 px-3 py-1 text-[10px] font-black uppercase tracking-[0.18em] text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-200">Default</span>
                                            @endif
                                        </div>
                                        <div class="mt-5 flex flex-wrap gap-2">
                                            @unless($savedAddress->is_default)
                                                <button type="button" wire:click="setDefaultAddress({{ $savedAddress->id }})" class="rounded-full bg-violet-50 px-4 py-2 text-sm font-black text-violet-700 dark:bg-violet-500/10 dark:text-violet-200">Set Default</button>
                                            @endunless
                                            <button type="button" wire:click="deleteAddress({{ $savedAddress->id }})" class="rounded-full bg-rose-50 px-4 py-2 text-sm font-black text-rose-600 dark:bg-rose-500/10 dark:text-rose-200">Remove</button>
                                        </div>
                                    </div>
                                @empty
                                    <div class="rounded-[1.5rem] border border-dashed border-slate-300 bg-white px-5 py-12 text-center text-sm {{ $muted }} dark:border-white/10 dark:bg-slate-950 md:col-span-2">
                                        No saved addresses yet. Add one here and checkout will pick it up automatically.
                                    </div>
                                @endforelse
                            </div>
                        </section>
                    @endif

                    @if($activeTab === 'orders')
                        <section class="space-y-4">
                            <h2 class="text-2xl font-black text-slate-950 dark:text-white">Orders</h2>
                            @forelse($orders as $order)
                                <div class="{{ $panel }}">
                                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                        <div>
                                            <p class="font-black text-slate-950 dark:text-white">#{{ $order->order_number ?? $order->id }}</p>
                                            <p class="mt-1 text-sm {{ $muted }}">{{ $order->created_at->format('M d, Y h:i A') }}</p>
                                        </div>
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600 dark:bg-slate-900 dark:text-slate-200">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span>
                                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600 dark:bg-slate-900 dark:text-slate-200">{{ ucfirst($order->payment_status ?? 'unpaid') }}</span>
                                            <span class="font-black text-slate-950 dark:text-white">Rs {{ number_format($order->total ?? 0, 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="rounded-2xl border border-dashed border-slate-300 px-5 py-12 text-center text-sm {{ $muted }} dark:border-white/10">No orders yet.</div>
                            @endforelse
                        </section>
                    @endif

                    @if($activeTab === 'wishlist')
                        <section>
                            <h2 class="text-2xl font-black text-slate-950 dark:text-white">Wishlist</h2>
                            <div class="mt-5 grid gap-4 md:grid-cols-2">
                                @forelse($wishlistProducts as $product)
                                    <div class="{{ $panel }}">
                                        <p class="text-xs font-bold uppercase tracking-[0.18em] {{ $muted }}">{{ $product->brand?->name ?? 'Product' }}</p>
                                        <h3 class="mt-2 font-black text-slate-950 dark:text-white">{{ $product->name }}</h3>
                                        <p class="mt-2 text-sm {{ $muted }}">Rs {{ number_format($product->selling_price, 2) }}</p>
                                        <a wire:navigate href="{{ url('/products/'.$product->id) }}" class="mt-4 inline-flex rounded-full bg-violet-600 px-4 py-2 text-sm font-black text-white">View Product</a>
                                    </div>
                                @empty
                                    <div class="rounded-2xl border border-dashed border-slate-300 px-5 py-12 text-center text-sm {{ $muted }} dark:border-white/10 md:col-span-2">No saved items yet.</div>
                                @endforelse
                            </div>
                        </section>
                    @endif

                    @if($activeTab === 'reviews')
                        <section>
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                <h2 class="text-2xl font-black text-slate-950 dark:text-white">Reviews</h2>
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
                                            <button type="button" wire:click="cancelReview" class="rounded-full bg-slate-100 px-5 py-2.5 text-sm font-black text-slate-600">Cancel</button>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="mt-5 space-y-4">
                                @forelse($reviews as $review)
                                    <div class="{{ $panel }}">
                                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                            <div>
                                                <p class="font-black text-slate-950 dark:text-white">{{ $review->stock?->name ?? 'Product Review' }}</p>
                                                <p class="mt-1 text-sm {{ $muted }}">{{ $review->title ?: 'No title' }}</p>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-700">{{ $review->rating }}/5</span>
                                                <button type="button" wire:click="editReview({{ $review->id }})" class="text-sm font-black text-violet-600">Edit</button>
                                                <button type="button" wire:click="deleteReview({{ $review->id }})" class="text-sm font-black text-rose-500">Delete</button>
                                            </div>
                                        </div>
                                        <p class="mt-4 text-sm leading-7 {{ $muted }}">{{ $review->body }}</p>
                                    </div>
                                @empty
                                    <div class="rounded-2xl border border-dashed border-slate-300 px-5 py-12 text-center text-sm {{ $muted }} dark:border-white/10">No reviews yet.</div>
                                @endforelse
                            </div>
                        </section>
                    @endif

                    @if($activeTab === 'settings')
                        <section>
                            <h2 class="text-2xl font-black text-slate-950 dark:text-white">Profile Settings</h2>
                            <div class="mt-5 grid gap-4 md:grid-cols-2">
                                <div><label class="text-xs font-bold uppercase tracking-wide {{ $muted }}">Name</label><input wire:model="name" type="text" class="{{ $input }} mt-2">@error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror</div>
                                <div><label class="text-xs font-bold uppercase tracking-wide {{ $muted }}">Email</label><input wire:model="email" type="email" class="{{ $input }} mt-2">@error('email')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror</div>
                                <div><label class="text-xs font-bold uppercase tracking-wide {{ $muted }}">Phone</label><input wire:model="phone" type="text" class="{{ $input }} mt-2"></div>
                                <div><label class="text-xs font-bold uppercase tracking-wide {{ $muted }}">Birthday</label><input wire:model="dob" type="date" class="{{ $input }} mt-2"></div>
                                <div class="md:col-span-2"><label class="text-xs font-bold uppercase tracking-wide {{ $muted }}">Profile Address Note</label><textarea wire:model="address" rows="3" class="{{ $input }} mt-2 resize-none"></textarea></div>
                            </div>
                            <div class="{{ $panel }} mt-6">
                                <p class="font-black text-slate-950 dark:text-white">Notification preferences</p>
                                <div class="mt-4 grid gap-3 sm:grid-cols-3">
                                    @foreach(['email_offers' => 'Email Offers', 'sms_alerts' => 'SMS Alerts', 'order_updates' => 'Order Updates'] as $property => $label)
                                        <label class="flex items-center gap-3 rounded-2xl bg-slate-50 px-4 py-3 text-sm font-bold text-slate-600 dark:bg-slate-900 dark:text-slate-200">
                                            <input wire:model="{{ $property }}" type="checkbox" class="rounded border-slate-300 text-violet-600 focus:ring-violet-500">
                                            {{ $label }}
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            <button type="button" wire:click="saveProfile" wire:loading.attr="disabled" class="mt-6 rounded-full bg-violet-600 px-6 py-3 text-sm font-black text-white">Save Profile</button>
                        </section>
                    @endif

                    @if($activeTab === 'security')
                        <section>
                            <h2 class="text-2xl font-black text-slate-950 dark:text-white">Security</h2>
                            <div class="mt-5 grid gap-4 md:grid-cols-2">
                                <div class="md:col-span-2"><label class="text-xs font-bold uppercase tracking-wide {{ $muted }}">Current Password</label><input wire:model="current_password" type="{{ $showCurrentPw ? 'text' : 'password' }}" class="{{ $input }} mt-2">@error('current_password')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror</div>
                                <div><label class="text-xs font-bold uppercase tracking-wide {{ $muted }}">New Password</label><input wire:model="password" type="{{ $showNewPw ? 'text' : 'password' }}" class="{{ $input }} mt-2">@error('password')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror</div>
                                <div><label class="text-xs font-bold uppercase tracking-wide {{ $muted }}">Confirm Password</label><input wire:model="password_confirmation" type="password" class="{{ $input }} mt-2"></div>
                            </div>
                            <div class="mt-6 flex flex-wrap gap-3">
                                <button type="button" wire:click="$toggle('showCurrentPw')" class="rounded-full bg-slate-100 px-4 py-2 text-sm font-black text-slate-600 dark:bg-slate-900 dark:text-slate-200">Show Current</button>
                                <button type="button" wire:click="$toggle('showNewPw')" class="rounded-full bg-slate-100 px-4 py-2 text-sm font-black text-slate-600 dark:bg-slate-900 dark:text-slate-200">Show New</button>
                                <button type="button" wire:click="updatePassword" class="rounded-full bg-violet-600 px-6 py-3 text-sm font-black text-white">Update Password</button>
                            </div>
                        </section>
                    @endif
                </main>
            </div>
        </section>
    </div>
</div>

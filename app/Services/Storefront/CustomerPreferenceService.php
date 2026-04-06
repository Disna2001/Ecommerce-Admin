<?php

namespace App\Services\Storefront;

use App\Models\Discount;
use App\Models\Order;
use App\Models\Review;
use App\Models\Stock;
use App\Models\StorefrontNotification;
use App\Models\User;
use Illuminate\Support\Collection;

class CustomerPreferenceService
{
    public function recordProductView(Stock $product, ?User $user = null): void
    {
        $this->storeSignals($product, $user, [
            'recently_viewed_product_ids' => $product->id,
            'favorite_category_ids' => $product->category_id,
            'favorite_brand_ids' => $product->brand_id,
            'price_watch_map' => [$product->id => $this->currentProductPrice($product)],
        ]);
    }

    public function recordWishlistIntent(Stock $product, ?User $user = null): void
    {
        $this->storeSignals($product, $user, [
            'favorite_category_ids' => $product->category_id,
            'favorite_brand_ids' => $product->brand_id,
            'price_watch_map' => [$product->id => $this->currentProductPrice($product)],
        ]);
    }

    public function getProfile(?User $user = null): array
    {
        $user ??= auth()->user();

        $viewedIds = collect($this->getPreferenceValue('recently_viewed_product_ids', [], $user));
        $favoriteCategoryIds = collect($this->getPreferenceValue('favorite_category_ids', [], $user));
        $favoriteBrandIds = collect($this->getPreferenceValue('favorite_brand_ids', [], $user));

        if ($user) {
            $purchasedIds = Order::query()
                ->where('user_id', $user->id)
                ->with('items:id,order_id,stock_id')
                ->get()
                ->flatMap(fn (Order $order) => $order->items->pluck('stock_id'))
                ->filter()
                ->values();

            $reviewedIds = Review::query()
                ->where('user_id', $user->id)
                ->pluck('stock_id');

            $stocks = Stock::query()
                ->whereIn('id', $purchasedIds->merge($reviewedIds)->merge($viewedIds)->filter()->unique()->values())
                ->get(['id', 'category_id', 'brand_id']);

            $favoriteCategoryIds = $favoriteCategoryIds
                ->merge($stocks->pluck('category_id'))
                ->filter()
                ->unique()
                ->values();

            $favoriteBrandIds = $favoriteBrandIds
                ->merge($stocks->pluck('brand_id'))
                ->filter()
                ->unique()
                ->values();

            $viewedIds = $viewedIds
                ->merge($purchasedIds)
                ->merge($reviewedIds)
                ->filter()
                ->unique()
                ->values();
        }

        return [
            'recently_viewed_product_ids' => $viewedIds->take(18)->all(),
            'favorite_category_ids' => $favoriteCategoryIds->take(12)->all(),
            'favorite_brand_ids' => $favoriteBrandIds->take(12)->all(),
            'price_watch_map' => $this->getPreferenceValue('price_watch_map', [], $user),
            'read_storefront_notification_ids' => $this->getPreferenceValue('read_storefront_notification_ids', [], $user),
            'notification_seen_at' => $this->getPreferenceValue('notification_seen_at', null, $user),
        ];
    }

    public function getRecommendedProducts(?User $user = null, int $limit = 6): Collection
    {
        $profile = $this->getProfile($user);

        $query = Stock::query()
            ->with(['brand', 'category'])
            ->where('status', 'active');

        if (!empty($profile['favorite_category_ids']) || !empty($profile['favorite_brand_ids'])) {
            $query->where(function ($builder) use ($profile) {
                if (!empty($profile['favorite_category_ids'])) {
                    $builder->whereIn('category_id', $profile['favorite_category_ids']);
                }

                if (!empty($profile['favorite_brand_ids'])) {
                    $builder->orWhereIn('brand_id', $profile['favorite_brand_ids']);
                }
            });
        }

        if (!empty($profile['recently_viewed_product_ids'])) {
            $query->whereNotIn('id', array_slice($profile['recently_viewed_product_ids'], 0, 8));
        }

        return $query
            ->latest()
            ->take($limit)
            ->get();
    }

    public function getNotificationItems(?User $user = null, int $limit = 6): Collection
    {
        $profile = $this->getProfile($user);
        $favoriteCategoryIds = $profile['favorite_category_ids'];
        $favoriteBrandIds = $profile['favorite_brand_ids'];
        $trackedProductIds = collect($profile['recently_viewed_product_ids'])
            ->merge(session('wishlist', []))
            ->filter()
            ->unique()
            ->take(12)
            ->values();

        $trackedProducts = Stock::query()
            ->with(['brand', 'category'])
            ->whereIn('id', $trackedProductIds)
            ->get()
            ->keyBy('id');

        $items = collect()
            ->merge($this->buildNewProductNotifications($favoriteCategoryIds, $favoriteBrandIds))
            ->merge($this->buildDealNotifications($favoriteCategoryIds, $favoriteBrandIds, $trackedProducts))
            ->merge($this->buildInventoryNotifications($trackedProducts))
            ->merge($this->buildPriceDropNotifications($trackedProducts, $profile['price_watch_map'] ?? []))
            ->merge($this->buildOrderNotifications($user));

        if ($user) {
            return $this->persistNotificationItems($user, $items, $limit);
        }

        return $items
            ->unique('id')
            ->sortByDesc(fn (array $item) => optional($item['created_at'])->timestamp ?? 0)
            ->map(function (array $item) use ($profile) {
                $item['read'] = collect($profile['read_storefront_notification_ids'] ?? [])->contains($item['id']);

                return $item;
            })
            ->values()
            ->take($limit);
    }

    public function unreadNotificationCount(?User $user = null): int
    {
        return $this->getNotificationItems($user)->where('read', false)->count();
    }

    public function markNotificationRead(string $id, ?User $user = null): void
    {
        $user ??= auth()->user();

        if ($user) {
            StorefrontNotification::query()
                ->where('user_id', $user->id)
                ->where('notification_key', $id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            return;
        }

        $this->pushPreferenceArrayValue('read_storefront_notification_ids', $id, $user, 60);
    }

    public function markNotificationsSeen(?User $user = null): void
    {
        $user ??= auth()->user();
        $now = now()->toIso8601String();
        $notificationIds = $this->getNotificationItems($user, 24)->pluck('id')->all();

        if ($user) {
            StorefrontNotification::query()
                ->where('user_id', $user->id)
                ->whereIn('notification_key', $notificationIds)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            $preferences = $user->preferences ?? [];
            $preferences['notification_seen_at'] = $now;
            $preferences['read_storefront_notification_ids'] = collect($preferences['read_storefront_notification_ids'] ?? [])
                ->merge($notificationIds)
                ->filter()
                ->unique()
                ->take(60)
                ->values()
                ->all();
            $user->update(['preferences' => $preferences]);
            return;
        }

        session([
            'storefront_notification_seen_at' => $now,
            'storefront_read_notification_ids' => collect(session('storefront_read_notification_ids', []))
                ->merge($notificationIds)
                ->filter()
                ->unique()
                ->take(60)
                ->values()
                ->all(),
        ]);
    }

    protected function storeSignals(Stock $product, ?User $user, array $signals): void
    {
        $user ??= auth()->user();

        if ($user) {
            $preferences = $user->preferences ?? [];

            foreach ($signals as $key => $value) {
                if ($key === 'price_watch_map') {
                    $preferences[$key] = array_merge($preferences[$key] ?? [], $value);
                    continue;
                }

                $preferences[$key] = collect($preferences[$key] ?? [])
                    ->prepend($value)
                    ->filter()
                    ->unique()
                    ->take($key === 'recently_viewed_product_ids' ? 18 : 12)
                    ->values()
                    ->all();
            }

            $user->update(['preferences' => $preferences]);
            return;
        }

        foreach ($signals as $key => $value) {
            $sessionKey = match ($key) {
                'recently_viewed_product_ids' => 'storefront_recently_viewed_product_ids',
                'favorite_category_ids' => 'storefront_favorite_category_ids',
                'favorite_brand_ids' => 'storefront_favorite_brand_ids',
                'price_watch_map' => 'storefront_price_watch_map',
                default => $key,
            };

            if ($key === 'price_watch_map') {
                session([$sessionKey => array_merge(session($sessionKey, []), $value)]);
                continue;
            }

            session([
                $sessionKey => collect(session($sessionKey, []))
                    ->prepend($value)
                    ->filter()
                    ->unique()
                    ->take($key === 'recently_viewed_product_ids' ? 18 : 12)
                    ->values()
                    ->all(),
            ]);
        }
    }

    protected function getPreferenceValue(string $key, mixed $default, ?User $user = null): mixed
    {
        $user ??= auth()->user();

        if ($user) {
            return $user->preferences[$key] ?? $default;
        }

        $sessionKey = match ($key) {
            'recently_viewed_product_ids' => 'storefront_recently_viewed_product_ids',
            'favorite_category_ids' => 'storefront_favorite_category_ids',
            'favorite_brand_ids' => 'storefront_favorite_brand_ids',
            'notification_seen_at' => 'storefront_notification_seen_at',
            'price_watch_map' => 'storefront_price_watch_map',
            'read_storefront_notification_ids' => 'storefront_read_notification_ids',
            default => $key,
        };

        return session($sessionKey, $default);
    }

    protected function buildNewProductNotifications(array $favoriteCategoryIds, array $favoriteBrandIds): Collection
    {
        return Stock::query()
            ->with(['brand', 'category'])
            ->where('status', 'active')
            ->when(!empty($favoriteCategoryIds) || !empty($favoriteBrandIds), function ($query) use ($favoriteCategoryIds, $favoriteBrandIds) {
                $query->where(function ($builder) use ($favoriteCategoryIds, $favoriteBrandIds) {
                    if (!empty($favoriteCategoryIds)) {
                        $builder->whereIn('category_id', $favoriteCategoryIds);
                    }

                    if (!empty($favoriteBrandIds)) {
                        $builder->orWhereIn('brand_id', $favoriteBrandIds);
                    }
                });
            })
            ->latest()
            ->take(3)
            ->get()
            ->map(fn (Stock $product) => $this->makeNotification(
                id: 'product:new:'.$product->id,
                type: 'product',
                title: 'New match for your interests',
                body: $product->name.' just landed in '.($product->category?->name ?? 'the catalog').'.',
                actionUrl: url('/products/'.$product->id),
                createdAt: $product->created_at,
                accent: 'indigo',
                label: 'New'
            ));
    }

    protected function buildDealNotifications(array $favoriteCategoryIds, array $favoriteBrandIds, Collection $trackedProducts): Collection
    {
        $trackedCategoryIds = $trackedProducts->pluck('category_id')->filter()->unique()->values()->all();
        $candidateCategoryIds = collect($favoriteCategoryIds)->merge($trackedCategoryIds)->filter()->unique()->all();

        return Discount::query()
            ->active()
            ->latest('updated_at')
            ->take(8)
            ->get()
            ->map(function (Discount $discount) use ($candidateCategoryIds, $trackedProducts) {
                $matchedProduct = null;

                if ($discount->scope === 'product') {
                    $matchedProduct = $trackedProducts->get((int) $discount->scope_id)
                        ?? Stock::query()->with(['brand', 'category'])->find($discount->scope_id);
                }

                if ($discount->scope === 'category' && in_array((int) $discount->scope_id, $candidateCategoryIds, true)) {
                    $matchedProduct = $trackedProducts->firstWhere('category_id', (int) $discount->scope_id)
                        ?? Stock::query()->with(['brand', 'category'])->where('category_id', $discount->scope_id)->where('status', 'active')->latest()->first();
                }

                if ($discount->scope === 'all') {
                    $matchedProduct = $trackedProducts->first()
                        ?? Stock::query()->with(['brand', 'category'])->where('status', 'active')->latest()->first();
                }

                if (!$matchedProduct) {
                    return null;
                }

                $value = $discount->type === 'percentage'
                    ? rtrim(rtrim(number_format((float) $discount->value, 2), '0'), '.').'% off'
                    : 'Rs '.number_format((float) $discount->value, 2).' off';

                return $this->makeNotification(
                    id: 'deal:'.$discount->id.':'.$matchedProduct->id,
                    type: 'deal',
                    title: 'Deal ready for you',
                    body: $discount->name.' gives '.$value.' on '.$matchedProduct->name.'.',
                    actionUrl: url('/products/'.$matchedProduct->id),
                    createdAt: $discount->updated_at ?? $discount->created_at,
                    accent: 'emerald',
                    label: strtoupper($discount->code ?: 'Deal')
                );
            })
            ->filter();
    }

    protected function buildInventoryNotifications(Collection $trackedProducts): Collection
    {
        return $trackedProducts
            ->filter(fn (Stock $product) => (int) $product->quantity > 0)
            ->sortByDesc(fn (Stock $product) => (int) $product->quantity)
            ->take(2)
            ->map(fn (Stock $product) => $this->makeNotification(
                id: 'stock:return:'.$product->id,
                type: 'stock',
                title: ((int) $product->quantity <= max(1, (int) $product->reorder_level))
                    ? 'Running low again'
                    : 'Back in stock',
                body: $product->name.' is available now with '.(int) $product->quantity.' item(s) ready to order.',
                actionUrl: url('/products/'.$product->id),
                createdAt: $product->updated_at ?? $product->created_at,
                accent: 'sky',
                label: 'Stock'
            ));
    }

    protected function buildPriceDropNotifications(Collection $trackedProducts, array $priceWatchMap): Collection
    {
        return $trackedProducts
            ->map(function (Stock $product) use ($priceWatchMap) {
                $previousPrice = (float) ($priceWatchMap[$product->id] ?? 0);
                $currentPrice = $this->currentProductPrice($product);

                if ($previousPrice <= 0 || $currentPrice >= $previousPrice) {
                    return null;
                }

                $savedAmount = $previousPrice - $currentPrice;

                return $this->makeNotification(
                    id: 'price-drop:'.$product->id.':'.number_format($currentPrice, 2, '.', ''),
                    type: 'price-drop',
                    title: 'Price dropped on a saved item',
                    body: $product->name.' is now Rs '.number_format($currentPrice, 2).' with Rs '.number_format($savedAmount, 2).' saved.',
                    actionUrl: url('/products/'.$product->id),
                    createdAt: $product->updated_at ?? $product->created_at,
                    accent: 'amber',
                    label: 'Price drop'
                );
            })
            ->filter();
    }

    protected function buildOrderNotifications(?User $user = null): Collection
    {
        if (!$user) {
            return collect();
        }

        return $user->orders()
            ->latest()
            ->take(2)
            ->get()
            ->map(fn (Order $order) => $this->makeNotification(
                id: 'order:'.$order->id.':'.$order->status,
                type: 'order',
                title: 'Order update available',
                body: 'Order #'.$order->order_number.' is currently '.str_replace('_', ' ', $order->status).'.',
                actionUrl: route('profile.index', ['tab' => 'orders']),
                createdAt: $order->updated_at ?? $order->created_at,
                accent: 'violet',
                label: 'Order'
            ));
    }

    protected function persistNotificationItems(User $user, Collection $items, int $limit): Collection
    {
        $items
            ->unique('id')
            ->each(function (array $item) use ($user) {
                StorefrontNotification::query()->updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'notification_key' => $item['id'],
                    ],
                    [
                        'type' => $item['type'],
                        'label' => $item['label'],
                        'accent' => $item['accent'],
                        'title' => $item['title'],
                        'body' => $item['body'],
                        'action_url' => $item['action_url'],
                        'notified_at' => $item['created_at'] ?? now(),
                        'payload' => [
                            'source' => 'storefront_preference_engine',
                        ],
                    ]
                );
            });

        return StorefrontNotification::query()
            ->where('user_id', $user->id)
            ->latest('notified_at')
            ->take($limit)
            ->get()
            ->map(fn (StorefrontNotification $notification) => [
                'id' => $notification->notification_key,
                'type' => $notification->type,
                'title' => $notification->title,
                'body' => $notification->body,
                'action_url' => $notification->action_url ?: url('/products'),
                'created_at' => $notification->notified_at ?? $notification->created_at,
                'accent' => $notification->accent,
                'label' => $notification->label ?: ucfirst($notification->type),
                'read' => filled($notification->read_at),
            ]);
    }

    protected function makeNotification(
        string $id,
        string $type,
        string $title,
        string $body,
        string $actionUrl,
        mixed $createdAt,
        string $accent,
        string $label
    ): array {
        return [
            'id' => $id,
            'type' => $type,
            'title' => $title,
            'body' => $body,
            'action_url' => $actionUrl,
            'created_at' => $createdAt,
            'accent' => $accent,
            'label' => $label,
        ];
    }

    protected function pushPreferenceArrayValue(string $key, mixed $value, ?User $user = null, int $limit = 12): void
    {
        $user ??= auth()->user();

        if ($user) {
            $preferences = $user->preferences ?? [];
            $preferences[$key] = collect($preferences[$key] ?? [])
                ->prepend($value)
                ->filter()
                ->unique()
                ->take($limit)
                ->values()
                ->all();
            $user->update(['preferences' => $preferences]);

            return;
        }

        $sessionKey = match ($key) {
            'read_storefront_notification_ids' => 'storefront_read_notification_ids',
            default => $key,
        };

        session([
            $sessionKey => collect(session($sessionKey, []))
                ->prepend($value)
                ->filter()
                ->unique()
                ->take($limit)
                ->values()
                ->all(),
        ]);
    }

    protected function currentProductPrice(Stock $product): float
    {
        return (float) app(ProductPricingService::class)->finalPriceForProduct($product);
    }
}

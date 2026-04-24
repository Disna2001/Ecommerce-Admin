<?php

namespace App\Livewire\Storefront;

use App\Services\Storefront\CustomerPreferenceService;
use App\Services\Storefront\StorefrontDataService;
use Livewire\Attributes\On;
use Livewire\Component;

class HeaderBar extends Component
{
    public int $cartCount = 0;
    public int $wishCount = 0;

    #[On('cart-updated')]
    public function refreshCartCount(?int $count = null): void
    {
        $this->cartCount = $count ?? (int) collect(session('cart', []))->sum('quantity');
    }

    #[On('wishlist-updated')]
    public function refreshWishlistCount(?int $count = null): void
    {
        $this->wishCount = $count ?? (int) count(session('wishlist', []));
    }

    public function mount(): void
    {
        $this->refreshCartCount();
        $this->refreshWishlistCount();
    }

    public function markNotificationsSeen(CustomerPreferenceService $preferenceService): void
    {
        $preferenceService->markNotificationsSeen(auth()->user());
    }

    public function markNotificationRead(string $id, CustomerPreferenceService $preferenceService): void
    {
        $preferenceService->markNotificationRead($id, auth()->user());
    }

    public function render(StorefrontDataService $storefrontDataService, CustomerPreferenceService $preferenceService)
    {
        $layout = $storefrontDataService->getSharedLayoutData();
        $recommended = $storefrontDataService->enrichForStorefrontCards(
            $preferenceService->getRecommendedProducts(auth()->user(), 4)
        );

        return view('livewire.storefront.header-bar', [
            'layout' => $layout,
            'notifications' => $preferenceService->getNotificationItems(auth()->user()),
            'unreadNotifications' => $preferenceService->unreadNotificationCount(auth()->user()),
            'recommended' => $recommended,
        ]);
    }
}

<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use App\Models\Order;
use App\Models\Stock;
use App\Models\Review;
use App\Models\Address;

class UserProfile extends Component
{
    use WithFileUploads;

    public string $activeTab = 'overview';

    // ── Profile photo ─────────────────────────────────────────
    public $profile_photo;

    // ── Settings form ─────────────────────────────────────────
    public string $name          = '';
    public string $email         = '';
    public string $phone         = '';
    public string $dob           = '';
    public string $address       = '';

    // ── Notification prefs — passed as plain bools ────────────
    public bool $email_offers  = true;
    public bool $sms_alerts    = false;
    public bool $order_updates = true;

    // ── Password form ─────────────────────────────────────────
    public string $current_password      = '';
    public string $password              = '';
    public string $password_confirmation = '';
    public bool   $showCurrentPw         = false;
    public bool   $showNewPw             = false;

    // ── Address form ─────────────────────────────────────────
    public bool   $showAddressForm = false;
    public string $addr_name       = '';
    public string $addr_phone      = '';
    public string $addr_address    = '';
    public string $addr_city       = '';
    public string $addr_postal     = '';
    public bool   $addr_is_default = false;

    // ── Card form ────────────────────────────────────────────
    public bool   $showCardForm  = false;
    public string $card_number   = '';
    public string $card_name     = '';
    public string $card_expiry   = '';
    public string $card_cvv      = '';

    // ── Reviews ───────────────────────────────────────────────
    public ?int   $editingReviewId = null;
    public bool   $showReviewForm  = false;
    public int    $review_rating   = 5;
    public string $review_title    = '';
    public string $review_body     = '';
    public ?int   $review_stock_id = null;
    public string $reviewFilter    = 'all';

    // ── Security ──────────────────────────────────────────────
    public string $logoutPassword = '';

    // ─────────────────────────────────────────────────────────
    public function mount(): void
    {
        $this->activeTab = request('tab', 'overview');
        $this->fillProfileFields();
    }

    private function fillProfileFields(): void
    {
        $user = Auth::user();
        $this->name          = $user->name;
        $this->email         = $user->email;
        $this->phone         = $user->phone  ?? '';
        $this->dob           = $user->dob ? $user->dob->format('Y-m-d') : '';
        $this->address       = $user->address ?? '';
        $prefs               = $user->preferences ?? [];
        $this->email_offers  = (bool) ($prefs['email_offers']  ?? true);
        $this->sms_alerts    = (bool) ($prefs['sms_alerts']    ?? false);
        $this->order_updates = (bool) ($prefs['order_updates'] ?? true);
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    // ══════════════════════════════════════════════════════════
    // PROFILE PHOTO
    // ══════════════════════════════════════════════════════════
    public function updatedProfilePhoto(): void
    {
        $this->validate([
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ]);
    }

    public function savePhoto(): void
    {
        $this->validate([
            'profile_photo' => 'required|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ]);

        $user = Auth::user();

        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        $path = $this->profile_photo->store('profile-photos', 'public');
        $user->update(['profile_photo_path' => $path]);

        $this->profile_photo = null;
        $this->dispatch('notify', type: 'success', message: 'Profile photo updated!');
    }

    public function removePhoto(): void
    {
        $user = Auth::user();
        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
            $user->update(['profile_photo_path' => null]);
        }
        $this->dispatch('notify', type: 'info', message: 'Profile photo removed.');
    }

    // ══════════════════════════════════════════════════════════
    // PROFILE SETTINGS
    // ══════════════════════════════════════════════════════════
    public function saveProfile(): void
    {
        $user = Auth::user();
        $this->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'required|email|unique:users,email,' . $user->id,
            'phone'   => 'nullable|string|max:20',
            'dob'     => 'nullable|date',
            'address' => 'nullable|string|max:500',
        ]);

        $preferences = array_merge($user->preferences ?? [], [
            'email_offers'  => $this->email_offers,
            'sms_alerts'    => $this->sms_alerts,
            'order_updates' => $this->order_updates,
        ]);

        $user->update([
            'name'        => $this->name,
            'email'       => $this->email,
            'phone'       => $this->phone    ?: null,
            'dob'         => $this->dob      ?: null,
            'address'     => $this->address  ?: null,
            'preferences' => $preferences,
        ]);

        $this->dispatch('notify', type: 'success', message: 'Profile updated successfully!');
    }

    // ══════════════════════════════════════════════════════════
    // PASSWORD
    // ══════════════════════════════════════════════════════════
    public function updatePassword(): void
    {
        $this->validate([
            'current_password'      => 'required',
            'password'              => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
            'password_confirmation' => 'required',
        ]);

        $user = Auth::user();
        if (!Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'Current password is incorrect.');
            return;
        }

        $user->update(['password' => Hash::make($this->password)]);
        $this->reset(['current_password', 'password', 'password_confirmation']);
        $this->dispatch('notify', type: 'success', message: 'Password updated!');
    }

    // ══════════════════════════════════════════════════════════
    // ADDRESS
    // ══════════════════════════════════════════════════════════
    public function saveAddress(): void
    {
        $this->validate([
            'addr_name'    => 'required|string|max:100',
            'addr_phone'   => 'required|string|max:20',
            'addr_address' => 'required|string|max:500',
            'addr_city'    => 'required|string|max:100',
            'addr_postal'  => 'nullable|string|max:20',
        ]);

        if ($this->addr_is_default) {
            Address::where('user_id', auth()->id())->update(['is_default' => false]);
        }

        Address::create([
            'user_id'     => auth()->id(),
            'name'        => $this->addr_name,
            'phone'       => $this->addr_phone,
            'address'     => $this->addr_address,
            'city'        => $this->addr_city,
            'postal_code' => $this->addr_postal,
            'is_default'  => $this->addr_is_default,
        ]);

        $this->reset(['addr_name','addr_phone','addr_address','addr_city','addr_postal','addr_is_default']);
        $this->showAddressForm = false;
        $this->dispatch('notify', type: 'success', message: 'Address saved!');
    }

    public function setDefaultAddress(int $id): void
    {
        $address = Address::where('user_id', auth()->id())->findOrFail($id);

        Address::where('user_id', auth()->id())->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        $fullAddress = trim($address->address . ', ' . $address->city);
        Auth::user()->update([
            'phone' => $address->phone ?: Auth::user()->phone,
            'address' => $fullAddress,
        ]);

        $this->phone = $address->phone ?: $this->phone;
        $this->address = $fullAddress;

        $this->dispatch('notify', type: 'success', message: 'Default address updated.');
    }

    public function deleteAddress(int $id): void
    {
        Address::where('user_id', auth()->id())->findOrFail($id)->delete();

        $this->dispatch('notify', type: 'info', message: 'Address removed.');
    }

    // ══════════════════════════════════════════════════════════
    // SOCIAL ACCOUNTS — disconnect (connect is handled by OAuth)
    // ══════════════════════════════════════════════════════════
    public function disconnectSocial(string $provider): void
    {
        $user            = Auth::user();
        $providers       = ['google','facebook','github'];
        $connectedCount  = collect($providers)->filter(fn($p) => !empty($user->{$p.'_id'}))->count();

        if ($connectedCount === 1 && empty($user->password)) {
            $this->dispatch('notify', type: 'error',
                message: 'Set a password before disconnecting your only login method.');
            return;
        }

        $user->disconnectSocial($provider);
        $this->dispatch('notify', type: 'success', message: ucfirst($provider) . ' disconnected.');
    }

    // ══════════════════════════════════════════════════════════
    // REVIEWS
    // ══════════════════════════════════════════════════════════
    public function editReview(int $id): void
    {
        $review = Review::where('user_id', auth()->id())->findOrFail($id);
        $this->editingReviewId = $id;
        $this->review_rating   = $review->rating;
        $this->review_title    = $review->title ?? '';
        $this->review_body     = $review->body;
        $this->review_stock_id = $review->stock_id;
        $this->showReviewForm  = true;
    }

    public function openNewReview(int $stockId): void
    {
        $this->editingReviewId = null;
        $this->review_rating   = 5;
        $this->review_title    = '';
        $this->review_body     = '';
        $this->review_stock_id = $stockId;
        $this->showReviewForm  = true;
        $this->activeTab       = 'reviews';
    }

    public function saveReview(): void
    {
        $this->validate([
            'review_rating' => 'required|integer|min:1|max:5',
            'review_title'  => 'nullable|string|max:200',
            'review_body'   => 'required|string|min:10|max:2000',
        ]);

        if ($this->editingReviewId) {
            Review::where('user_id', auth()->id())
                ->findOrFail($this->editingReviewId)
                ->update([
                    'rating' => $this->review_rating,
                    'title'  => $this->review_title ?: null,
                    'body'   => $this->review_body,
                ]);
            $msg = 'Review updated!';
        } else {
            if (Review::where('user_id', auth()->id())->where('stock_id', $this->review_stock_id)->exists()) {
                $this->dispatch('notify', type: 'error', message: 'You already reviewed this product.');
                return;
            }
            Review::create([
                'user_id'     => auth()->id(),
                'stock_id'    => $this->review_stock_id,
                'rating'      => $this->review_rating,
                'title'       => $this->review_title ?: null,
                'body'        => $this->review_body,
                'is_approved' => true,
                'approved_at' => now(),
            ]);
            $msg = 'Review submitted!';
        }

        $this->reset(['editingReviewId','review_rating','review_title','review_body','review_stock_id']);
        $this->showReviewForm = false;
        $this->dispatch('notify', type: 'success', message: $msg);
    }

    public function deleteReview(int $id): void
    {
        Review::where('user_id', auth()->id())->findOrFail($id)->delete();
        $this->dispatch('notify', type: 'info', message: 'Review deleted.');
    }

    public function cancelReview(): void
    {
        $this->reset(['editingReviewId','review_rating','review_title','review_body','review_stock_id']);
        $this->showReviewForm = false;
    }

    // ══════════════════════════════════════════════════════════
    // SECURITY
    // ══════════════════════════════════════════════════════════
    public function toggle2FA(bool $enabled): void
    {
        $this->dispatch('notify', type: 'info',
            message: $enabled ? '2FA setup — integrate with Laravel Fortify.' : '2FA disabled.');
    }

    public function logoutOtherDevices(): void
    {
        if (!Hash::check($this->logoutPassword, Auth::user()->password)) {
            $this->dispatch('notify', type: 'error', message: 'Incorrect password.');
            return;
        }
        Auth::logoutOtherDevices($this->logoutPassword);
        $this->logoutPassword = '';
        $this->dispatch('notify', type: 'success', message: 'All other sessions signed out.');
    }

    // ══════════════════════════════════════════════════════════
    // RENDER — all data passed explicitly, NO $this->x in blade
    // ══════════════════════════════════════════════════════════
    public function render()
    {
        $user = Auth::user();

        $orders = class_exists(Order::class)
            ? Order::with('items')->where('user_id', $user->id)->latest()->get()
            : collect();

        $wishlistIds      = session('wishlist', []);
        $wishlistProducts = !empty($wishlistIds)
            ? Stock::with('brand')->whereIn('id', $wishlistIds)->where('status', 'active')->get()
            : collect();

        $addresses = Address::where('user_id', $user->id)
            ->orderByDesc('is_default')
            ->latest()
            ->get();

        $reviewsQuery = Review::with('stock')->where('user_id', $user->id);
        if ($this->reviewFilter === 'approved') $reviewsQuery->where('is_approved', true);
        if ($this->reviewFilter === 'pending')  $reviewsQuery->where('is_approved', false);
        $reviews = $reviewsQuery->latest()->get();

        $reviewedStockIds   = Review::where('user_id', $user->id)->pluck('stock_id')->toArray();
        $purchasedStockIds  = class_exists(\App\Models\OrderItem::class)
            ? \App\Models\OrderItem::whereHas('order', fn($q) =>
                $q->where('user_id', $user->id)->whereIn('status', ['completed','delivered'])
              )->pluck('stock_id')->unique()->toArray()
            : [];
        $unreviewedStockIds  = array_diff($purchasedStockIds, $reviewedStockIds);
        $unreviewedProducts  = !empty($unreviewedStockIds)
            ? Stock::whereIn('id', $unreviewedStockIds)->get()
            : collect();

        // Pass toggle states as plain bools so blade never needs $this->
        $email_offers  = $this->email_offers;
        $sms_alerts    = $this->sms_alerts;
        $order_updates = $this->order_updates;

        return view('livewire.profile.user-profile', compact(
            'user', 'orders', 'wishlistProducts', 'addresses',
            'reviews', 'unreviewedProducts',
            'email_offers', 'sms_alerts', 'order_updates'
        ));
    }
}

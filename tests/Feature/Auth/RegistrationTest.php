<?php

namespace Tests\Feature\Auth;

use App\Mail\WelcomeAccountMail;
use App\Models\Merchant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Livewire\Volt\Volt;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response
            ->assertOk()
            ->assertSeeVolt('pages.auth.register');
    }

    public function test_first_registered_user_is_redirected_to_admin_dashboard(): void
    {
        Mail::fake();

        $component = Volt::test('pages.auth.register')
            ->set('name', 'Test User')
            ->set('email', 'test@example.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password');

        $component->call('register');

        $component->assertRedirect(route('admin.dashboard', absolute: false));

        $this->assertAuthenticated();
        $this->assertSame('admin', User::first()->user_type);
        Mail::assertSent(WelcomeAccountMail::class);
    }

    public function test_regular_users_can_register_and_receive_welcome_email(): void
    {
        Mail::fake();

        User::factory()->create();

        $component = Volt::test('pages.auth.register')
            ->set('name', 'Regular User')
            ->set('email', 'regular@example.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->set('user_type', 'regular');

        $component->call('register');

        $component->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticated();
        $this->assertSame('regular', User::where('email', 'regular@example.com')->first()->user_type);
        Mail::assertSent(WelcomeAccountMail::class, function (WelcomeAccountMail $mail) {
            return $mail->hasTo('regular@example.com');
        });
    }

    public function test_merchants_can_register_with_documents_and_are_marked_pending_review(): void
    {
        Mail::fake();
        Storage::fake('public');

        User::factory()->create();

        $component = Volt::test('pages.auth.register')
            ->set('name', '  Merchant User  ')
            ->set('email', 'MERCHANT@EXAMPLE.COM')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->set('user_type', 'merchant')
            ->set('shop_name', '  Display Hub  ')
            ->set('phone_number', ' +94 77 123 4567 ')
            ->set('nic_number', ' 2000 123456  ')
            ->set('br_number', ' br 7788 ')
            ->set('shop_address', '  25 Main Street  ')
            ->set('nic_image', UploadedFile::fake()->image('nic.jpg'))
            ->set('shop_image', UploadedFile::fake()->image('shop.jpg'))
            ->set('merchant_selfie', UploadedFile::fake()->image('selfie.jpg'));

        $component->call('register');

        $component->assertRedirect(route('dashboard', absolute: false));

        $user = User::where('email', 'merchant@example.com')->first();

        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user);
        $this->assertSame('merchant', $user->user_type);
        $this->assertTrue($user->hasRole('Merchant'));

        $merchant = Merchant::where('user_id', $user->id)->first();

        $this->assertNotNull($merchant);
        $this->assertSame('2000123456', $merchant->nic_number);
        $this->assertSame('BR7788', $merchant->br_number);
        $this->assertSame('Display Hub', $merchant->shop_name);
        $this->assertSame('+94 77 123 4567', $merchant->phone_number);
        $this->assertSame('25 Main Street', $merchant->shop_address);
        $this->assertSame('pending', $merchant->verification_status);

        Storage::disk('public')->assertExists($merchant->nic_image_path);
        Storage::disk('public')->assertExists($merchant->shop_image_path);
        Storage::disk('public')->assertExists($merchant->merchant_selfie_path);

        Mail::assertSent(WelcomeAccountMail::class, function (WelcomeAccountMail $mail) {
            return $mail->hasTo('merchant@example.com');
        });
    }
}

<?php

namespace Tests\Feature\Auth;

use App\Mail\WelcomeAccountMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
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
}

<?php

namespace Tests\Feature;

use App\Models\NotificationOutbox;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class OperationsCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_system_health_check_warns_for_sync_queue_and_outputs_json(): void
    {
        config([
            'queue.default' => 'sync',
            'app.debug' => true,
        ]);

        NotificationOutbox::create([
            'channel' => 'email',
            'recipient' => 'test@example.com',
            'status' => 'failed',
            'attempt_count' => 2,
            'last_attempt_at' => now(),
            'queued_at' => now()->subMinutes(20),
            'failed_at' => now(),
        ]);

        $this->artisan('system:health-check')
            ->expectsOutputToContain('System Health Check')
            ->expectsOutputToContain('System health check finished with warnings.')
            ->assertExitCode(1);
    }

    public function test_admin_restore_access_command_assigns_admin_permissions(): void
    {
        $user = User::factory()->create([
            'email' => 'restore@example.com',
        ]);

        $this->artisan('admin:restore-access restore@example.com')
            ->expectsOutputToContain('Access restored.')
            ->assertSuccessful();

        $user->refresh();

        $this->assertTrue($user->hasRole('Admin'));
        $this->assertTrue($user->can('view dashboard'));
        $this->assertTrue(Permission::where('name', 'view system health')->exists());
    }

    public function test_system_prepare_hosting_command_completes_with_skip_storage_link(): void
    {
        $this->artisan('system:prepare-hosting --skip-storage-link')
            ->expectsOutputToContain('Preparing the application for hosted deployment...')
            ->expectsOutputToContain('Hosting preparation finished.')
            ->assertSuccessful();
    }
}

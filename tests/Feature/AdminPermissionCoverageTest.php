<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class AdminPermissionCoverageTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_pages_require_their_specific_permissions(): void
    {
        $user = User::factory()->create();

        $routes = [
            'admin.dashboard' => 'view dashboard',
            'admin.orders' => 'view orders',
            'admin.stocks' => 'view inventory',
            'admin.stock-movements' => 'view stock movements',
            'admin.invoices' => 'view invoices',
            'admin.settings' => 'view settings',
            'admin.activity-logs' => 'view activity logs',
            'admin.notification-outbox' => 'view notification outbox',
            'admin.system-health' => 'view system health',
        ];

        foreach ($routes as $routeName => $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);

            $this->actingAs($user)
                ->get(route($routeName))
                ->assertForbidden();

            $user->givePermissionTo($permissionName);

            $this->actingAs($user)
                ->get(route($routeName))
                ->assertOk();

            $user->revokePermissionTo($permissionName);
        }
    }
}

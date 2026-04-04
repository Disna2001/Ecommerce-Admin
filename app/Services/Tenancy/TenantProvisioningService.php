<?php

namespace App\Services\Tenancy;

use App\Models\SiteSetting;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class TenantProvisioningService
{
    public function provisionTenant(Tenant $tenant, bool $bootstrapSettings = true, bool $createAdmin = false, array $adminData = []): void
    {
        if ($bootstrapSettings) {
            $this->bootstrapSettings($tenant);
        }

        if ($createAdmin) {
            $this->createInitialAdmin($tenant, $adminData);
        }
    }

    protected function bootstrapSettings(Tenant $tenant): void
    {
        $sourceTenant = Tenant::query()
            ->where('id', '!=', $tenant->id)
            ->where('is_default', true)
            ->first();

        $settings = SiteSetting::withoutGlobalScopes()
            ->when($sourceTenant, fn ($query) => $query->where('tenant_id', $sourceTenant->id))
            ->get(['key', 'value', 'type', 'group', 'label']);

        foreach ($settings as $setting) {
            SiteSetting::withoutGlobalScopes()->updateOrCreate(
                ['tenant_id' => $tenant->id, 'key' => $setting->key],
                [
                    'tenant_id' => $tenant->id,
                    'value' => $setting->value,
                    'type' => $setting->type,
                    'group' => $setting->group,
                    'label' => $setting->label,
                ]
            );
        }

        SiteSetting::withoutGlobalScopes()->updateOrCreate(
            ['tenant_id' => $tenant->id, 'key' => 'site_name'],
            [
                'tenant_id' => $tenant->id,
                'value' => $tenant->name,
                'type' => 'text',
                'group' => 'branding',
                'label' => 'Site Name',
            ]
        );

        SiteSetting::withoutGlobalScopes()->updateOrCreate(
            ['tenant_id' => $tenant->id, 'key' => 'app_public_url'],
            [
                'tenant_id' => $tenant->id,
                'value' => 'https://' . $tenant->primary_domain,
                'type' => 'text',
                'group' => 'general',
                'label' => 'App Public URL',
            ]
        );
    }

    protected function createInitialAdmin(Tenant $tenant, array $adminData): void
    {
        if (blank($adminData['email'] ?? null)) {
            return;
        }

        $user = User::query()->firstOrCreate(
            ['email' => $adminData['email']],
            [
                'tenant_id' => $tenant->id,
                'name' => $adminData['name'] ?: $tenant->name . ' Admin',
                'password' => Hash::make($adminData['password']),
                'user_type' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        if (!$user->tenant_id) {
            $user->tenant_id = $tenant->id;
            $user->save();
        }

        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);

        if (!$user->hasRole($adminRole->name)) {
            $user->assignRole($adminRole);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Tenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenant = Tenant::query()->where('is_default', true)->first() ?? Tenant::query()->first();

        // Check if any admin exists
        $adminExists = User::whereHas('roles', function($query) {
            $query->where('name', 'admin');
        })->exists();

        // Only create admin if no admin exists
        if (!$adminExists) {
            $admin = User::create([
                'name' => 'Super Admin',
                'email' => 'admin@example.com',
                'tenant_id' => $tenant?->id,
                'password' => Hash::make('password'),
                'user_type' => 'admin',
                'email_verified_at' => now(),
            ]);

            // Assign admin role
            $adminRole = Role::firstOrCreate(['name' => 'admin']);
            $admin->assignRole($adminRole);

            $this->command->info('Admin user created successfully!');
            $this->command->info('Email: admin@example.com');
            $this->command->info('Password: password');
        } else {
            $this->command->info('Admin user already exists. Skipping...');
        }
    }
}

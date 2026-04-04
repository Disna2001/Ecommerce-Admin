<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Services\Tenancy\TenantManager;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Spatie\Permission\Models\Role;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'password' => $this->passwordRules(),
        ])->validate();

        $user = User::create([
            'tenant_id' => app(TenantManager::class)->currentId(),
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);

        // Check if Admin role exists, if not create it
        if (!Role::where('name', 'Admin')->exists()) {
            Role::create(['name' => 'Admin']);
            Role::create(['name' => 'Manager']);
            Role::create(['name' => 'Staff']);
        }
        
        // Assign Admin role
        $user->assignRole('Admin');

        return $user;
    }
}

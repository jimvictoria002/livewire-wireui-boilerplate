<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => config('app.admin.email')],
            [
                'name' => 'System Admin',
                'password' => Hash::make(config('app.admin.password')),
            ]
        );

        if ($admin && !$admin->hasRole(UserRole::ADMIN->value)) {
            $admin->assignRole(UserRole::ADMIN->value);
        }
    }
}

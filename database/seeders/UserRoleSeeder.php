<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Services\PermissionService;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserRoleSeeder extends Seeder
{
    public function run(PermissionService $permissionService): void
    {
        foreach ($permissionService->getAllPermissions() as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
        foreach (UserRole::cases() as $roleEnum) {
            $role = Role::firstOrCreate(['name' => $roleEnum->value]);
            $role->syncPermissions($permissionService->getDefaultPermissionsByRole($roleEnum));
        }
    }
}

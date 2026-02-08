<?php

namespace App\Services;

use App\Enums\Permission\UserPermission;
use App\Enums\UserRole;

class PermissionService
{
    public function __construct()
    {
    }

    public function getEnumValues(string $enumClass): array
    {
        return array_map(
            fn($case) => $case->value,
            $enumClass::cases()
        );
    }

    public function getAllPermissions(): array
    {
        return array_merge(
            $this->getEnumValues(UserPermission::class),
        );
    }

    public function getDefaultPermissionsByRole(UserRole $role): array
    {
        return match ($role) {
            UserRole::ADMIN => $this->getAllPermissions(),
        };
    }
}

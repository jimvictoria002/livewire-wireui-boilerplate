<?php

namespace App\Enums\Permission;

enum UserPermission: string
{
    case USER_VIEW = 'View Users';
    case USER_MANAGE = 'Manage Users';
}

<?php

namespace App\Auth\Domain\ValueObject;

enum RoleType : string {
    case User = 'user_role';
    case Admin = 'admin_role';
}
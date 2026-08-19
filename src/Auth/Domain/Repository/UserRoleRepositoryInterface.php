<?php

namespace App\Auth\Domain\Repository;

use App\Auth\Domain\ValueObject\RoleId;
use App\Auth\Domain\ValueObject\UserId;
use App\Auth\Domain\ValueObject\RoleType;

interface UserRoleRepositoryInterface {
    public function assignRoleToUser(UserId $userId, RoleId $roleId): void;
    public function findByUserId(UserId $userId): array;  // Array<Role>
    public function removeRole(UserId $userId, RoleId $roleId): void;
    public function hasRole(UserId $userId, RoleType $roleType): bool;
}

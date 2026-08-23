<?php

namespace App\Auth\Domain\Repository;

use App\Auth\Domain\ValueObject\RoleId;
use App\Auth\Domain\ValueObject\UserId;
use App\Auth\Domain\ValueObject\RoleType;
use App\Auth\Domain\Entity\Role;

interface UserRoleRepositoryInterface {
    public function assignRoleToUser(UserId $userId, RoleId $roleId): void;
    /** @return array<Role> */
    public function findByUserId(UserId $userId): array;
    public function removeRole(UserId $userId, RoleId $roleId): void;
    public function hasRole(UserId $userId, RoleType $roleType): bool;
}

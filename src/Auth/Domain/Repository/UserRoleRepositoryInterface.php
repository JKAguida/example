<?php

namespace App\Auth\Domain\Repository;


interface UserRoleRepositoryInterface {
    public function assignRoleToUser(UserId $userId, RoleId $roleId): void;
    public function findByUserId(UserId $userId): array;  // Array<Role>
    public function removeRole(UserId $userId, RoleId $roleId): void;
    public function hasRole(UserId $userId, RoleType $roleType): bool;
}

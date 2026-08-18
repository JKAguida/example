<?php

namespace App\Auth\Domain\Repository;
use App\Auth\Domain\Entity\Role;
use App\Auth\Domain\ValueObject\RoleId;
use App\Auth\Domain\ValueObject\RoleType;


interface RoleRepositoryInterface {
    public function save(Role $role):void;
    public function findByRoleId(RoleId $roleId):?Role;
    public function findByRoleType(RoleType $roleType):?Role;
}
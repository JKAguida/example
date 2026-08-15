<?php

namespace App\Auth\Domain\Repository;
use App\Auth\Domain\Entity\Role;
use App\Auth\Domain\ValueObject\RoleId;


interface RoleRepositoryInterface {
    public function save(Role $role):void;
    public function findByRoleId(RoleId $roleId):?Role;
    public function reconstitute():Role;
}
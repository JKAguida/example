<?php

namespace App\Auth\Domain\Entity;
use App\Auth\Domain\ValueObject\RoleId;
use App\Auth\Domain\ValueObject\RoleType;


final class Role {
    private function __construct(
        private readonly RoleId $roleId,
        private readonly RoleType $roleType
    ){}

    public static function create(RoleType $type):self{
        return new self(
            RoleId::generate(),
            $type
        );
    }

    public static function reconstitute(RoleId $roleId, RoleType $roleType):self{
        return new self($roleId,$roleType);
    }

    public function roleId():RoleId{
        return $this->roleId;
    }

    public function roleType():RoleType{
        return $this->roleType;
    }
}
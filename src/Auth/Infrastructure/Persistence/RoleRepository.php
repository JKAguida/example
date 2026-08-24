<?php

namespace App\Auth\Infrastructure\Persistence;

use App\Auth\Domain\Repository\RoleRepositoryInterface;
use App\Auth\Domain\Entity\Role;
use App\Auth\Domain\ValueObject\RoleId;
use App\Auth\Domain\ValueObject\RoleType;
use PDO;



final class RoleRepository implements RoleRepositoryInterface {
    public function __construct( private readonly PDO $pdo ) {}

    public function save(Role $role):void {
        $data = [
            "roleId" => $role->roleId()->value(),
            "roleType" => $role->roleType()->value
        ];
        $sql = "INSERT INTO roles (roleId, roleType)
                VALUES (:roleId, :roleType)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
    }

    public function findByRoleId(RoleId $roleId):?Role {
        $data = [
            "roleId" => $roleId->value(),
        ];
        $sql = "SELECT * FROM roles WHERE roleId = :roleId LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
        $role = $stmt->fetch(PDO::FETCH_ASSOC);
        if($role){
            return self::reconstitute($role);
        }else{
            return null;
        }
        
    }

    public function findByRoleType(RoleType $roleType):?Role {
        $data = [
            "roleType" => $roleType->value,
        ];
        $sql = "SELECT * FROM roles WHERE roleType = :roleType LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
        $role = $stmt->fetch(PDO::FETCH_ASSOC);
        if($role){
            return self::reconstitute($role);
        }else{
            return null;
        }
        
    }

    /** @param array{roleId:string,roleType:string} $role */
    private static function reconstitute(array $role):Role{
        return Role::reconstitute(
            RoleId::fromString($role["roleId"]),
            RoleType::from($role["roleType"])
        );
    }
}
<?php

namespace App\Auth\Infrastructure\Persistence;
use App\Auth\Domain\Repository\UserRoleRepositoryInterface;
use App\Auth\Domain\ValueObject\UserId;
use App\Auth\Domain\ValueObject\RoleId;
use App\Auth\Domain\ValueObject\RoleType;
use App\Auth\Domain\Entity\Role;
use PDO;

final class UserRoleRepository implements UserRoleRepositoryInterface {
    public function __construct(
        private readonly PDO $pdo
    ){}

    public function assignRoleToUser(UserId $userId, RoleId $roleId): void {
        $data = [
            "roleId" => $roleId->value(),
            "userId" => $userId->value()
        ];
        $sql = "INSERT INTO user_roles (roleId,userId)
                VALUES (:roleId, :userId)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
    }

    public function findByUserId(UserId $userId): array {
        $data = [
            "userId" => $userId->value()
        ];
        $sql = "SELECT ur.roleId, r.roleType FROM user_roles AS ur
                INNER JOIN roles AS r ON ur.roleId = r.roleId
                WHERE ur.userId = :userId";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
        $roles_db = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $roles_entity = [];
        foreach($roles_db as $row){
            $role = Role::reconstitute(
                RoleId::fromString($row['roleId']),
                RoleType::from($row['roleType'])
            );
            $roles_entity[] = $role;
        }
        return $roles_entity;
    }

    public function removeRole(UserId $userId, RoleId $roleId): void {
        $data = [
            "roleId" => $roleId->value(),
            "userId" => $userId->value()
        ];
        $sql = "DELETE FROM user_roles WHERE userId = :userId AND roleId = :roleId";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
    }

    public function hasRole(UserId $userId, RoleType $roleType): bool {
        $data = [
            "userId" => $userId->value(),
            "roleType" => $roleType->value,
        ];
        $sql = "SELECT ur.roleId FROM user_roles AS ur
                INNER JOIN roles AS r ON r.roleId = ur.roleId
                WHERE ur.userId = :userId AND r.roleType = :roleType";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
        $role = $stmt->fetch(PDO::FETCH_ASSOC);
        return $role ? true : false ;
    }
}

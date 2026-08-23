<?php

namespace App\Auth\Application\UseCase;

use App\Auth\Domain\Repository\UserRepositoryInterface;
use App\Auth\Domain\Repository\UserRoleRepositoryInterface;
use App\Auth\Application\DTO\GetUserResponseDTO;
use App\Auth\Domain\ValueObject\UserId;
use App\Auth\Domain\Exception\InvalidTokenException;


final class GetUser {
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly UserRoleRepositoryInterface $userRoleRepository,
    ){}

    public function execute(string $strUserId): GetUserResponseDTO {
        // Buscar al usuario
        $userId = UserId::fromString($strUserId);
        $userExist = $this->userRepository->findById($userId);
        if(!$userExist) throw new InvalidTokenException();
        $roles = $this->userRoleRepository->findByUserId($userId);
        $roleTypes = [];
        foreach($roles as $role){
            $roleTypes[]=$role->roleType()->value;
        }
        // Retornar el DTO
        return new GetUserResponseDTO(
            $userExist->userName()->value(),
            $userExist->lastName()->value(),
            $userExist->email()->value(),
            $roleTypes
        );
    }
}
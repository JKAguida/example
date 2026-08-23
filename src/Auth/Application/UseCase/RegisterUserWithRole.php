<?php

namespace App\Auth\Application\UseCase;

use App\Auth\Application\DTO\RegisterUserRequestDTO;

use App\Auth\Domain\ValueObject\RoleType;
use App\Auth\Domain\ValueObject\UserId;

use App\Shared\Application\Port\EventDispatcherInterface;
use App\Shared\Application\Port\TransactionManagerInterface;

use App\Auth\Domain\Repository\UserRoleRepositoryInterface;
use App\Auth\Domain\Repository\RoleRepositoryInterface;

use App\Shared\Domain\Exception\CorruptedPersistedDataException;

use App\Auth\Application\Service\CreateUserService;
use App\Shared\Domain\Exception\NotAuthorizedException;
use App\Shared\Domain\Exception\InvalidInputException;





final class RegisterUserWithRole {
    public function __construct(
        private readonly CreateUserService $createUserService,
        private readonly RoleRepositoryInterface $roleRepository,
        private readonly UserRoleRepositoryInterface $userRoleRepository,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly TransactionManagerInterface $transactionManager
    ){}

    public function execute(RegisterUserRequestDTO $userData,string $strRoleType, string $userRequestedId):void{
        $userAdminId = UserId::fromString($userRequestedId);
        //validar el rol del ejecutor
        $isAdmin = $this->userRoleRepository->hasRole($userAdminId,RoleType::Admin);
        if(!$isAdmin) throw new NotAuthorizedException();
    
        $roleType = RoleType::tryFrom($strRoleType);
        if(!$roleType) throw new InvalidInputException("El rol solicitado no es válido");
        // Recuperar el role
        $role = $this->roleRepository->findByRoleType($roleType);
        if(!$role) throw new CorruptedPersistedDataException("El tipo de rol no fue encontrado.");
        
        $this->transactionManager->begin();
        try {
            // Guardar la entidad
            $user = $this->createUserService->execute($userData);
            // asigación de rol
            $this->userRoleRepository->assignRoleToUser($user->userId(),$role->roleId());
            $this->transactionManager->commit();
        }catch(\Throwable $e){
            $this->transactionManager->rollback();
            throw $e;
        }

        // Despachar eventos
        foreach ($user->pullDomainEvents() as $event) {
            $this->eventDispatcher->dispatch($event);
        }

    }
}
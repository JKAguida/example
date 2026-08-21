<?php

namespace App\Auth\Application\UseCase;

use App\Auth\Application\DTO\RegisterUserRequestDTO;

use App\Auth\Domain\ValueObject\RoleType;

use App\Shared\Application\Port\EventDispatcherInterface;
use App\Shared\Application\Port\TransactionManagerInterface;

use App\Auth\Domain\Repository\UserRoleRepositoryInterface;
use App\Auth\Domain\Repository\RoleRepositoryInterface;

use App\Shared\Domain\Exception\CorruptedPersistedDataException;

use App\Auth\Application\Service\CreateUserService;




final class CreateAdminUser {
    public function __construct(
        private readonly CreateUserService $createUserService,
        private readonly RoleRepositoryInterface $roleRepository,
        private readonly UserRoleRepositoryInterface $userRoleRepository,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly TransactionManagerInterface $transactionManager
    ){}

    public function execute(RegisterUserRequestDTO $userData):void{
        // Recuperar el role
        $role = $this->roleRepository->findByRoleType(RoleType::Admin);
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
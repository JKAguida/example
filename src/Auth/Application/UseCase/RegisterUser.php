<?php

namespace App\Auth\Application\UseCase;

use App\Auth\Application\DTO\RegisterUserRequestDTO;

use App\Shared\Application\Port\EventDispatcherInterface;
use App\Shared\Application\Port\TransactionManagerInterface;

use App\Auth\Application\Service\CreateUserService;



final class RegisterUser {
    public function __construct(
        private readonly CreateUserService $createUserService,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly TransactionManagerInterface $transactionManager
    ){}

    public function execute(RegisterUserRequestDTO $userData):void{
        $this->transactionManager->begin();
        try {
            // Guardar la entidad
            $user = $this->createUserService->execute($userData);
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
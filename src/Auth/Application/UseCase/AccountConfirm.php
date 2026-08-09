<?php

namespace App\Auth\Application\UseCase;

use App\Auth\Domain\ValueObject\TokenValue;
use App\Auth\Domain\ValueObject\TokenType;

use App\Auth\Domain\Repository\VerificationTokenRepositoryInterface;
use App\Auth\Domain\Repository\UserRepositoryInterface;

use App\Shared\Application\Port\TransactionManagerInterface;
use App\Auth\Domain\Exception\InvalidTokenException;
use App\Shared\Domain\Exception\CorruptedPersistedDataException;

final class AccountConfirm {
    
    public function __construct(
        private readonly VerificationTokenRepositoryInterface $verificationTokenRepository,
        private readonly UserRepositoryInterface $userRepository,
        private readonly TransactionManagerInterface $transactionManager
    ){}

    public function confirmAccount(string $tokenValue){
        $tknValueObj = TokenValue::fromString($tokenValue);
        $tokenType = TokenType::EmailConfirmation;


        $tokenExist = $this->verificationTokenRepository->findByTokenValue($tknValueObj);
        if(!$tokenExist) throw new InvalidTokenException("El token no existe");
        
        $tokenExist->ensureTokenValid($tokenType);

        $userExist = $this->userRepository->findById($tokenExist->userId());
        if (!$userExist) throw new CorruptedPersistedDataException("Usuario no encontrado: ".$tokenExist->userId()->value());
       
        $userExist->confirmAccount();

        $this->transactionManager->begin();
        try {
            $this->userRepository->save($userExist);
            $this->verificationTokenRepository->delete($tokenExist->tokenId());
            $this->transactionManager->commit();
        } catch (\Throwable $th) {
            $this->transactionManager->rollback();
            throw $th;
        }
    }
}
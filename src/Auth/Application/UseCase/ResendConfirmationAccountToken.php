<?php

namespace App\Auth\Application\UseCase;

use App\Auth\Domain\ValueObject\Email;
use App\Auth\Domain\Repository\UserRepositoryInterface;
use App\Auth\Domain\ValueObject\TokenType;
use App\Auth\Domain\Entity\VerificationToken;
use App\Shared\Application\Port\TransactionManagerInterface;


use App\Auth\Domain\Events\ConfirmationTokenResent;
use App\Auth\Domain\Repository\VerificationTokenRepositoryInterface;
use App\Shared\Application\Port\EventDispatcherInterface;

final class ResendConfirmationAccountToken {
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly VerificationTokenRepositoryInterface $verificationTokenRepository,
        private readonly TransactionManagerInterface $transactionManager,
        private readonly EventDispatcherInterface $eventDispatcher
    ){}

    public function execute(string $strEmail):void {
        $email = Email::create($strEmail);
        $userExists = $this->userRepository->findByEmail($email);
        if(!$userExists) return;
        if($userExists->isVerified()) return;
        $verificationToken = VerificationToken::create(
            TokenType::EmailConfirmation,
            $userExists->userId()
        );

        try {
            $this->transactionManager->begin();
            $this->verificationTokenRepository->deleteAllByTokenType($userExists->userId(),TokenType::EmailConfirmation);
            $this->verificationTokenRepository->save($verificationToken);
            $this->transactionManager->commit();
        } catch (\Throwable $th) {
            $this->transactionManager->rollback();
            throw $th;
        }
        $event = ConfirmationTokenResent::create(
            $userExists->userId(),
            $email,
            $userExists->userName(),
            $verificationToken->tokenValue()
        );
        $this->eventDispatcher->dispatch($event);
    }
}
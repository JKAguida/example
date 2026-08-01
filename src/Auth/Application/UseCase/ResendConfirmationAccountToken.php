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

    public function execute(string $strEmail) {
        $email = new Email($strEmail);
        $userExists = $userRepository.findByEmail($email);
        if(!$userExists) return;
        $verificationToken = VerificationToken::create(
            TokenType::EmailConfirmation,
            $userExists->userId()
        );

        try {
            $transactionManager->begin();
            $this->verificationTokenRepository->deleteAllByTokenType($userExists->userId(),TokenType::EmailConfirmation);
            $this->verificationTokenRepository->save($verificationToken);
            $transactionManager->commit();
        } catch (\Throwable $th) {
            $transactionManager->rollback();
            throw $th;
        }
        $event = ConfirmationTokenResent::create(
            $userExists->userId(),
            $email,
            $userExists->userName()
        );
        $this->eventDispatcher->dispatch($event);
    }
}
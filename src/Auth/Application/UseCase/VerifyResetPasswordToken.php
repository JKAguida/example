<?php

namespace App\Auth\Application\UseCase;

use App\Auth\Domain\ValueObject\TokenValue;
use App\Auth\Domain\ValueObject\TokenType;

use App\Auth\Domain\Repository\VerificationTokenRepositoryInterface;

use App\Auth\Domain\Exception\InvalidTokenException;

final class VerifyResetPasswordToken {
    
    public function __construct(
        private readonly VerificationTokenRepositoryInterface $verificationTokenRepository,
    ){}

    public function verifyToken(string $tokenValue){
        $tknValueObj = TokenValue::fromString($tokenValue);
        $tokenType = TokenType::PasswordRecovery;


        $tokenExist = $this->verificationTokenRepository->findByTokenValue($tknValueObj);
        if(!$tokenExist) throw new InvalidTokenException("El token no existe");
        $tokenExist->ensureTokenValid($tokenType);
    }
}
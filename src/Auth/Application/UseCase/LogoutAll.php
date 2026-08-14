<?php

namespace App\Auth\Application\UseCase;
use App\Auth\Domain\Repository\RefreshTokenRepositoryInterface;
use App\Shared\Application\Port\CookieManagerInterface;
use App\Auth\Domain\Exception\InvalidTokenException;
use App\Auth\Domain\ValueObject\TokenValue;



final class LogoutAll {
    public function __construct(
        private readonly RefreshTokenRepositoryInterface $tokenRefreshRepository,
        private readonly CookieManagerInterface $cookieManager
    ){}

    public function logoutAll():void {
        $refreshTokenValue = $this->cookieManager->get('refreshTokenJKApp');
        if(!$refreshTokenValue) throw new InvalidTokenException();
        $refreshToken = $this->tokenRefreshRepository->findByTokenValue(TokenValue::fromString($refreshTokenValue));
        if(!$refreshToken) throw new InvalidTokenException();
        $this->tokenRefreshRepository->deleteAll($refreshToken->userId());
        $this->cookieManager->delete('refreshTokenJKApp');
    }
}
<?php

namespace App\Auth\Application\UseCase;
use App\Auth\Domain\ValueObject\TokenValue;
use App\Auth\Domain\Repository\RefreshTokenRepositoryInterface;
use App\Shared\Application\Port\CookieManagerInterface;


final class Logout {
    public function __construct(
        private readonly RefreshTokenRepositoryInterface $tokenRefreshRepository,
        private readonly CookieManagerInterface $cookieManager
    ){}

    public function logout():void {
        $refreshTokenValue = $this->cookieManager->get('refreshTokenJKApp');
        if(!$refreshTokenValue) return;
        $this->cookieManager->delete('refreshTokenJKApp');
        $refreshToken = $this->tokenRefreshRepository->findByTokenValue(TokenValue::fromString($refreshTokenValue));
        if(!$refreshToken) return;
        $this->tokenRefreshRepository->delete($refreshToken->tokenId());
    }
}
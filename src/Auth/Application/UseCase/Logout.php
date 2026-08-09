<?php

namespace App\Auth\Application\UseCase;
use App\Auth\Domain\ValueObject\TokenValue;
use App\Auth\Domain\Exception\InvalidTokenException;
use App\Auth\Domain\Repository\RefreshTokenRepositoryInterface;
use App\Shared\Application\Port\CookieManagerInterface;


final class Logout {
    public function __construct(
        private readonly RefreshTokenRepositoryInterface $tokenRefreshRepository,
        private readonly CookieManagerInterface $cookieManager
    ){}

    public function logout(string $refreshTokenValue):void {
        $refreshToken = $this->tokenRefreshRepository->findByTokenValue(TokenValue::fromString($refreshTokenValue));
        if(!$refreshToken) throw new InvalidTokenException("Token no válido");
        $this->tokenRefreshRepository->delete($refreshToken->tokenId());
        $this->cookieManager->delete('refreshTokenJKApp');
    }
}
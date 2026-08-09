<?php

namespace App\Auth\Domain\Entity;

use App\Auth\Domain\ValueObject\TokenId;
use App\Auth\Domain\ValueObject\TokenValue;
use App\Auth\Domain\ValueObject\TokenExpiration;
use App\Auth\Domain\ValueObject\TokenType;
use App\Auth\Domain\ValueObject\UserId;
use App\Auth\Domain\Exception\InvalidTokenTypeException;
use App\Auth\Domain\Exception\TokenExpiredException;

final class VerificationToken {
    private function __construct(
        private readonly TokenId $tokenId,
        private readonly TokenValue $tokenValue,
        private readonly TokenExpiration $tokenExpiration,
        private readonly TokenType $tokenType,
        private readonly UserId $userId,
    ){}

    public static function create(
        TokenType $tokenType,
        UserId $userId,
    ) : self {
        $tokenId = TokenId::generate();
        $tokenValue = TokenValue::generate();
        $tokenExpiration = TokenExpiration::generate();
        return new self(
            $tokenId,
            $tokenValue,
            $tokenExpiration,
            $tokenType,
            $userId,
        );
    }

    public static function reconstitute(
        TokenId $tokenId,
        TokenValue $tokenValue,
        TokenExpiration $tokenExpiration,
        TokenType $tokenType,
        UserId $userId,
    ) : self {
        return new self(
            $tokenId,
            $tokenValue,
            $tokenExpiration,
            $tokenType,
            $userId,
        );
    }

    public function ensureTokenValid(TokenType $type) : void {
        if($this->tokenType() !== $type) throw new InvalidTokenTypeException("El tipo de token recibido no es correcto, se esperaba: ".$type->value." y llego un: ".$this->tokenType()->value);
        if($this->tokenExpiration()->isExpired()) throw new TokenExpiredException();
    }

    public function tokenId(): TokenId {return $this->tokenId; }
    public function tokenValue(): TokenValue { return  $this->tokenValue; }
    public function tokenExpiration(): TokenExpiration { return $this->tokenExpiration; }
    public function tokenType(): TokenType { return $this->tokenType; }
    public function userId(): UserId { return $this->userId; }


}
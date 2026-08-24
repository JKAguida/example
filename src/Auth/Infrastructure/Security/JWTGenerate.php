<?php

namespace App\Auth\Infrastructure\Security;

use App\Auth\Application\Security\TokenGeneratorInterface;
use Firebase\JWT\JWT;
use App\Auth\Domain\ValueObject\UserId;
use DateTimeImmutable;
use App\Shared\Infrastructure\Exception\BadConfigurationException;

final class JWTGenerate implements TokenGeneratorInterface {
    public function __construct(
        private readonly string $privateKey,
    ){}

    public function generate(UserId $userId):string {
        $now = new DateTimeImmutable();
        $exp = $now->modify("+15 minutes");
        $payload = [
            "sub"=>$userId->value(),
            "exp"=>$exp->getTimestamp()
        ];
        $key = file_get_contents($this->privateKey);
        if(!$key){
            throw new BadConfigurationException("No se recibio la private key correctamente");
        }
        $token = JWT::encode($payload,$key,'RS256');
        return $token;
    }
}


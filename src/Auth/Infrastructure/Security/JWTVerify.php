<?php

namespace App\Auth\Infrastructure\Security;

use App\Auth\Application\Security\TokenValidatorInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use DomainException;
use InvalidArgumentException;
use UnexpectedValueException;

use App\Auth\Domain\Exception\InvalidTokenException;
use App\Auth\Domain\Exception\TokenExpiredException;
use App\Shared\Infrastructure\Exception\BadConfigurationException;
use App\Auth\Application\Security\Exception\AccessTokenExpiredException;


final class JWTVerify implements TokenValidatorInterface {
    private readonly Key $key;
    public function __construct(
        private readonly string $publicKey,
    ){
        error_log("[JWT_PUBLIC_KEY]: ".$this->publicKey);

        $publicKeyContent = file_get_contents($this->publicKey);
        
        if(!$publicKeyContent){
            throw new BadConfigurationException(
                message: "La public key esta vacía."            
            );
        }
        $this->key = new Key($publicKeyContent, 'RS256');
    }

    /** @return array{sub:string,iat:int} */
    public function verify(string $token):array {
        try {
            $decoded = JWT::decode($token, $this->key);
            $decoded_array = (array) $decoded;
            return $decoded_array;
        } catch (DomainException $e) {
            // provided algorithm is unsupported OR
            // provided key is invalid OR
            // unknown error thrown in openSSL or libsodium OR
            // libsodium is required but not available.
            throw new BadConfigurationException(
                message: "Incompatibilidad criptográfica, falla en la clave, algoritmo o falta de estensiones para generar las claves.",
                previous: $e
            );
        } catch (SignatureInvalidException $e) {
            // provided JWT signature verification failed.
            throw new InvalidTokenException("El JWT no es válido, ha sido manipulado.");
        } catch (BeforeValidException $e) {
            // provided JWT is trying to be used before "nbf" claim OR
            // provided JWT is trying to be used before "iat" claim.
            //uso prematuro del JWT
            throw new InvalidTokenException("El JWT esta siendo usado prematuramente.");
        } catch (ExpiredException $e) {
            // provided JWT is trying to be used after "exp" claim.
            throw new AccessTokenExpiredException("El JWT ha expirado.");
        } catch (UnexpectedValueException $e) {
            // provided JWT is malformed OR
            // provided JWT is missing an algorithm / using an unsupported algorithm OR
            // provided JWT algorithm does not match provided key OR
            // provided key ID in key/key-array is empty or invalid.
            throw new InvalidTokenException("JWT malformado.");
        }
    }
}


<?php

namespace App\Auth\Infrastructure\Di;
use App\Shared\Infrastructure\Di\Container;


use App\Auth\Domain\Repository\UserRepositoryInterface; 
use App\Auth\Infrastructure\Persistence\UserRepository;
use App\Auth\Domain\Repository\VerificationTokenRepositoryInterface;
use App\Auth\Infrastructure\Persistence\VerificationTokenRepository;
use App\Auth\Domain\Repository\RoleRepositoryInterface; 
use App\Auth\Infrastructure\Persistence\RoleRepository;
use App\Auth\Domain\Repository\UserRoleRepositoryInterface;
use App\Auth\Infrastructure\Persistence\UserRoleRepository;
use App\Auth\Domain\Service\PasswordHashInterface;
use App\Auth\Infrastructure\Security\PasswordHash;

use App\Auth\Application\Security\TokenGeneratorInterface;
use App\Auth\Infrastructure\Security\JWTGenerate;
use App\Auth\Domain\Repository\RefreshTokenRepositoryInterface;
use App\Auth\Infrastructure\Persistence\RefreshTokenRepository;

use App\Auth\Application\Security\TokenValidatorInterface;
use App\Auth\Infrastructure\Security\JWTVerify;




final class AuthContainerConfig {
    private function __construct() {}

    public static function register(Container $c):void{
        $config = require(__DIR__ . '/../../../../config/auth.php');
        $privateKeyPath = $config['jwt']['private_key_path'];
        $publicKeyPath = $config['jwt']['public_key_path'];

        $classToInstance = [
            UserRepositoryInterface::class => UserRepository::class,
            RoleRepositoryInterface::class => RoleRepository::class,
            UserRoleRepositoryInterface::class => UserRoleRepository::class,
            VerificationTokenRepositoryInterface::class => VerificationTokenRepository::class,
            PasswordHashInterface::class => PasswordHash::class,
            RefreshTokenRepositoryInterface::class => RefreshTokenRepository::class,
            TokenGeneratorInterface::class => function() use($privateKeyPath) {
                return new JWTGenerate(
                    $privateKeyPath
                );
            },
            TokenValidatorInterface::class => function() use ($publicKeyPath){
                return new JWTVerify($publicKeyPath);
            },

        ];

        foreach($classToInstance as $key => $value){
            $c->bind($key,$value);
        }

    }

}
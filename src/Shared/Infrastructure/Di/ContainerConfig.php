<?php

namespace App\Shared\Infrastructure\Di;
use App\Shared\Infrastructure\Di\Container;

use App\Auth\Infrastructure\Controllers\RegisterController; 
use App\Auth\Application\UseCase\RegisterUser;
use App\Auth\Application\Service\CreateUserService;
use App\Auth\Domain\Service\VerifyEmailExist; 
use App\Auth\Infrastructure\EventListener\SendEmailConfirmation; 
use App\Shared\Application\Port\EventDispatcherInterface; 
use App\Auth\Infrastructure\EventDispatcher\EventDispatcher;
use App\Shared\Application\Port\TransactionManagerInterface; 
use App\Shared\Infrastructure\Persistence\TransactionManager;
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

use App\Auth\Infrastructure\Controllers\AccountConfirmController;
use App\Auth\Application\UseCase\AccountConfirm;

use App\Auth\Infrastructure\Controllers\LoginController;
use App\Auth\Application\UseCase\Login;
use App\Auth\Application\Security\TokenGeneratorInterface;
use App\Auth\Infrastructure\Security\JWTGenerate;
use App\Auth\Domain\Repository\RefreshTokenRepositoryInterface;
use App\Auth\Infrastructure\Persistence\RefreshTokenRepository;
use App\Shared\Application\Port\CookieManagerInterface;
use App\Shared\Infrastructure\Http\CookieManager;

use App\Auth\Infrastructure\Controllers\RefreshController;
use App\Auth\Application\UseCase\Refresh;

use App\Auth\Infrastructure\Controllers\LogoutController;
use App\Auth\Application\UseCase\Logout;

use App\Auth\Infrastructure\Controllers\LogoutAllController;
use App\Auth\Application\UseCase\LogoutAll;

use App\Auth\Infrastructure\Controllers\PasswordRecoveryController;
use App\Auth\Application\UseCase\PasswordRecovery;
use App\Auth\Infrastructure\EventListener\SendPasswordRecoveryEmail;

use App\Auth\Infrastructure\Controllers\VerifyResetPasswordTokenController;
use App\Auth\Application\UseCase\VerifyResetPasswordToken;

use App\Auth\Infrastructure\Controllers\ResetPasswordController;
use App\Auth\Application\UseCase\ResetPassword;

use App\Auth\Infrastructure\Controllers\ResendConfirmationAccountTokenController;
use App\Auth\Application\UseCase\ResendConfirmationAccountToken;
use App\Auth\Infrastructure\EventListener\ResendEmailConfirmationToken;

use App\Shared\Application\Port\MailerInterface;
use \App\Shared\Infrastructure\Mailer\SmtpMailer;

use App\Auth\Application\Security\TokenValidatorInterface;
use App\Auth\Infrastructure\Security\JWTVerify;

use App\Auth\Application\UseCase\CreateAdminUser;




final class ContainerConfig {
    private function __construct() {}

    public static function create():Container{
        $container = new Container();
        $config = require(__DIR__ . '/../../../../config/auth.php');
        $privateKeyPath = $config['jwt']['private_key_path'];
        $publicKeyPath = $config['jwt']['public_key_path'];

        $classToInstance = [
            RegisterController::class => RegisterController::class,
            RegisterUser::class => RegisterUser::class,
            CreateUserService::class => CreateUserService::class,
            EventDispatcherInterface::class => EventDispatcher::class,
            SendEmailConfirmation::class => SendEmailConfirmation::class,
            TransactionManagerInterface::class => TransactionManager::class,
            UserRepositoryInterface::class => UserRepository::class,
            RoleRepositoryInterface::class => RoleRepository::class,
            UserRoleRepositoryInterface::class => UserRoleRepository::class,
            VerificationTokenRepositoryInterface::class => VerificationTokenRepository::class,
            PasswordHashInterface::class => PasswordHash::class,
            VerifyEmailExist::class => VerifyEmailExist::class,
            
            AccountConfirmController::class => AccountConfirmController::class,
            AccountConfirm::class => AccountConfirm::class,

            LoginController::class => LoginController::class,
            Login::class => Login::class,
            RefreshTokenRepositoryInterface::class => RefreshTokenRepository::class,
            CookieManagerInterface::class => CookieManager::class,

            RefreshController::class => RefreshController::class,
            Refresh::class => Refresh::class,

            LogoutController::class => LogoutController::class,
            Logout::class => Logout::class,

            LogoutAllController::class => LogoutAllController::class,
            LogoutAll::class => LogoutAll::class,

            PasswordRecoveryController::class => PasswordRecoveryController::class,
            PasswordRecovery::class => PasswordRecovery::class,
            SendPasswordRecoveryEmail::class => SendPasswordRecoveryEmail::class,

            VerifyResetPasswordTokenController::class => VerifyResetPasswordTokenController::class,
            VerifyResetPasswordToken::class => VerifyResetPasswordToken::class,

            ResetPasswordController::class => ResetPasswordController::class,
            ResetPassword::class => ResetPassword::class,

            ResendConfirmationAccountTokenController::class => ResendConfirmationAccountTokenController::class,
            ResendConfirmationAccountToken::class => ResendConfirmationAccountToken::class,
            ResendEmailConfirmationToken::class => ResendEmailConfirmationToken::class,

            CreateAdminUser::class => CreateAdminUser::class,
            
            \PDO::class => function(){ 
                return new \PDO(
                    'mysql:host='.getenv('DB_HOST').';dbname='.getenv('DB_NAME').';charset=utf8mb4',
                    getenv('DB_USER'),
                    getenv('DB_PASSWORD')
                );
            },
            MailerInterface::class => function(){
                return new SmtpMailer(
                    getenv("SMTP_HOST"),
                    getenv("SMTP_USERNAME"),
                    getenv("SMTP_PASSWORD"),
                    (int) getenv("SMTP_PORT"),
                );
            },
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
            $container->bind($key,$value);
        }

        return $container;
    }

}
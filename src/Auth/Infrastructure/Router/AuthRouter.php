<?php

namespace App\Auth\Infrastructure\Router;
use App\Shared\Infrastructure\Router\Router;
use App\Auth\Infrastructure\Controllers\RegisterController;
use App\Auth\Infrastructure\Controllers\AccountConfirmController;
use App\Auth\Infrastructure\Controllers\LoginController;
use App\Auth\Infrastructure\Controllers\RefreshController;
use App\Auth\Infrastructure\Controllers\LogoutController;
use App\Auth\Infrastructure\Controllers\LogoutAllController;
use App\Auth\Infrastructure\Controllers\PasswordRecoveryController;
use App\Auth\Infrastructure\Controllers\VerifyResetPasswordTokenController;
use App\Auth\Infrastructure\Controllers\ResetPasswordController;
use App\Auth\Infrastructure\Controllers\ResendConfirmationAccountTokenController;
use App\Auth\Infrastructure\Controllers\RegisterUserWithRoleController;
use App\Auth\Infrastructure\Controllers\GetUserController;
use App\Auth\Infrastructure\Middleware\CheckAuthMiddleware;

/** 
 * @phpstan-type RouterSchema array{GET:array<string,array{controller:class-string,middlewares?:list<class-string>}>,POST:array<string,array{controller:class-string,middlewares?:list<class-string>}>} 
 * @phpstan-import-type RouteEntry from Router
 * */

final class AuthRouter {
    /** @var RouterSchema */
    private static array $routes = [
        'GET' => [
            "/auth/confirm" => [ 
                "controller" => AccountConfirmController::class
            ],
            "/auth/verify-reset-token" => [ 
                "controller" => VerifyResetPasswordTokenController::class
            ],
            "/auth/me" => [ 
                "controller" => GetUserController::class,
                "middlewares" => [CheckAuthMiddleware::class]
            ],
        ],
        'POST' => [
            "/auth/register" => [ 
                "controller"=>RegisterController::class
            ],
            "/auth/login" => [ 
                "controller" => LoginController::class
            ],
            "/auth/request-new-password" => [ 
                "controller" => PasswordRecoveryController::class
            ],
            "/auth/reset-password" => [ 
                "controller" => ResetPasswordController::class
            ],
            "/auth/logout" => [ 
                "controller" => LogoutController::class
            ],
            "/auth/logout-all-sessions" => [ 
                "controller" => LogoutAllController::class
            ],
            "/auth/resend-confirm-account" => [ 
                "controller" => ResendConfirmationAccountTokenController::class
            ],
            "/auth/refresh" => [ 
                "controller" => RefreshController::class
            ],
            "/auth/create-user" => [ 
                "controller" => RegisterUserWithRoleController::class,
                "middlewares" => [CheckAuthMiddleware::class]
            ],
        ]
    ];

    private function __construct(){}
    
    /** @return ?RouteEntry */
    public static function router(string $method, string $path):?array {
        return isset(self::$routes[$method][$path]) ? self::$routes[$method][$path] : null;
    }
}

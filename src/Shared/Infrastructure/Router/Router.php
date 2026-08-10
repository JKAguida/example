<?php

namespace App\Shared\Infrastructure\Router;
use App\Shared\Infrastructure\Http\Exception\InvalidPathException;

final class Router {
    private static array $routers = [
        "auth" => "App\Auth\Infrastructure\Router\AuthRouter"
    ];

    private function __construct(){}

    public static function resolve(string $method, string $path): string {
        $uri_parts = explode('/',$path);
        $bounded_context = $uri_parts[1];
        if(!isset(self::$routers[$bounded_context])) throw new InvalidPathException("El bounded context no esta definido.");
        return self::$routers[$bounded_context]::router($method,$path) ?? throw new InvalidPathException("Ruta no econtrada: ".$path." Método: ".$method);
    }

}
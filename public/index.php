<?php
date_default_timezone_set('UTC');
require_once __DIR__.'/../vendor/autoload.php';

use App\Shared\Infrastructure\Bootstrap\EnvironmentLoader;
use App\Shared\Infrastructure\Di\ContainerConfig;
use App\Shared\Infrastructure\Router\Router;
use App\Shared\Infrastructure\Middleware\CORSMiddleware;
use App\Shared\Infrastructure\Http\HandlerExceptions;


$request_method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['PATH_INFO'] ?? $_SERVER['REQUEST_URI'];
$path_clean = explode('?',$path)[0];

try {
    EnvironmentLoader::load();
    $container = ContainerConfig::create();
    CORSMiddleware::handle();

    $dispatcher = $container->get("App\Shared\Application\Port\EventDispatcherInterface");

    $dispatcher->addListener("App\Auth\Domain\Events\UserRegistered",$container->get("App\Auth\Infrastructure\EventListener\SendEmailConfirmation"));
    $dispatcher->addListener("App\Auth\Domain\Events\PasswordRecoveryRequested",$container->get("App\Auth\Infrastructure\EventListener\SendPasswordRecoveryEmail"));
    $dispatcher->addListener("App\Auth\Domain\Events\ConfirmationTokenResent",$container->get("App\Auth\Infrastructure\EventListener\ResendEmailConfirmationToken"));

    $controller = Router::resolve($request_method,$path_clean);
    $instance = $container->get($controller);
    $instance->execute();
} catch (\Throwable $th) {
    $handler = new HandlerExceptions();
    $response = $handler->handle($th,$request_method,$path_clean);
    $response->send();
}

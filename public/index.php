<?php
date_default_timezone_set('UTC');
require_once __DIR__.'/../vendor/autoload.php';

use App\Shared\Infrastructure\Bootstrap\EnvironmentLoader;
use App\Shared\Infrastructure\Di\ContainerConfig;
use App\Shared\Infrastructure\Router\Router;
use App\Shared\Infrastructure\Middleware\CORSMiddleware;
use App\Shared\Infrastructure\Http\HandlerExceptions;
use App\Auth\Domain\Events\UserRegistered;
use App\Auth\Infrastructure\EventListener\SendEmailConfirmation;
use App\Auth\Domain\Events\PasswordRecoveryRequested;
use App\Auth\Infrastructure\EventListener\SendPasswordRecoveryEmail;
use App\Auth\Domain\Events\ConfirmationTokenResent;
use App\Auth\Infrastructure\EventListener\ResendEmailConfirmationToken;
use App\Shared\Application\Port\EventDispatcherInterface;

$request_method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['PATH_INFO'] ?? $_SERVER['REQUEST_URI'];
$path_clean = explode('?',$path)[0];

try {
    EnvironmentLoader::load();
    $container = ContainerConfig::create();
    CORSMiddleware::handle();

    $dispatcher = $container->get(EventDispatcherInterface::class);

    $dispatcher->addListener(UserRegistered::class,$container->get(SendEmailConfirmation::class));
    $dispatcher->addListener(PasswordRecoveryRequested::class,$container->get(SendPasswordRecoveryEmail::class));
    $dispatcher->addListener(ConfirmationTokenResent::class,$container->get(ResendEmailConfirmationToken::class));

    $controller = Router::resolve($request_method,$path_clean);
    $instance = $container->get($controller);
    $instance->execute();
} catch (\Throwable $th) {
    $handler = new HandlerExceptions();
    $response = $handler->handle($th,$request_method,$path_clean);
    $response->send();
}

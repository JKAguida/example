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
use App\Shared\Infrastructure\Middleware\RequiresAuthenticationInterface;
use App\Shared\Infrastructure\Exception\BadConfigurationException;
use App\Auth\Infrastructure\Middleware\CheckAuthMiddleware;
use App\Shared\Infrastructure\Http\Request;

try {
    EnvironmentLoader::load();
    $container = ContainerConfig::create();
    CORSMiddleware::handle();

    $dispatcher = $container->get(EventDispatcherInterface::class);

    $dispatcher->addListener(UserRegistered::class,$container->get(SendEmailConfirmation::class));
    $dispatcher->addListener(PasswordRecoveryRequested::class,$container->get(SendPasswordRecoveryEmail::class));
    $dispatcher->addListener(ConfirmationTokenResent::class,$container->get(ResendEmailConfirmationToken::class));

    $req = $container->get(Request::class);
    $controller_data = Router::resolve($req->method(),$req->path());    

    if(isset($controller_data['middlewares'])){
        foreach($controller_data['middlewares'] as $value){
            $middleware = $container->get($value);
            $middleware->execute();
        }
    }

    $instance = $container->get($controller_data['controller']);

    if($instance instanceof RequiresAuthenticationInterface && !$req->userId()){
        throw new BadConfigurationException("Parece que no ha sido declarado el middleware en la ruta de este controlador. ".$controller_data['controller']);
    }
    
    $instance->execute();
} catch (\Throwable $th) {
    $request_method = $_SERVER['REQUEST_METHOD'];
    $path = $_SERVER['PATH_INFO'] ?? $_SERVER['REQUEST_URI'];
    $path_clean = explode('?',$path)[0];
    $handler = new HandlerExceptions();
    $response = $handler->handle($th,$request_method,$path_clean);
    $response->send();
}

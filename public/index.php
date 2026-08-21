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
use App\Auth\Infrastructure\Middleware\AuthControllerContextInterface;
use App\Shared\Infrastructure\Exception\BadConfigurationException;
use App\Auth\Infrastructure\Middleware\CheckAuthMiddleware;



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

    $controller_data = Router::resolve($request_method,$path_clean);
    $instance = $container->get($controller_data['controller']);
    
    $userId = null;

    if(isset($controller_data['middlewares'])){
        foreach($controller_data['middlewares'] as $value){
            $middleware = $container->get($value);
            if($middleware instanceof CheckAuthMiddleware){
                $userId = $middleware->execute();
            }else{
                $middleware->execute();
            }
        }
    }

    if($instance instanceof AuthControllerContextInterface && !$userId){
        throw new BadConfigurationException("Parece que no ha sido declarado el middleware en la ruta de este controlador. ".$controller_data['controller']);
    }
    if($instance instanceof AuthControllerContextInterface && $userId){
        $instance->setUserId($userId);
    }
    $instance->execute();
} catch (\Throwable $th) {
    $handler = new HandlerExceptions();
    $response = $handler->handle($th,$request_method,$path_clean);
    $response->send();
}

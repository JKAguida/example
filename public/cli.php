<?php
date_default_timezone_set('UTC');
require_once __DIR__.'/../vendor/autoload.php';

use App\Shared\Infrastructure\Bootstrap\EnvironmentLoader;
use App\Shared\Infrastructure\Di\ContainerConfig;
use App\Auth\Application\UseCase\CreateAdminUser;


try {
    EnvironmentLoader::load();
    $container = ContainerConfig::create();

    $dispatcher = $container->get("App\Shared\Application\Port\EventDispatcherInterface");
    $dispatcher->addListener("App\Auth\Domain\Events\UserRegistered",$container->get("App\Auth\Infrastructure\EventListener\SendEmailConfirmation"));

    switch ($argv[1]) {
        case "seed-roles":
            
            break;
        case "create-admin":
            CreateAdminUser::execute();
            break;
        default:
            echo("Argumento no válido");
            break;
    }
} catch (\Throwable $th) {
    $handler = new HandlerExceptions();
    $response = $handler->handle($th,$request_method,$path_clean);
    echo(json_encode($response));
}

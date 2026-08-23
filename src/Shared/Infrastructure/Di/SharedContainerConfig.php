<?php

namespace App\Shared\Infrastructure\Di;
use App\Shared\Infrastructure\Di\Container;

use App\Shared\Application\Port\EventDispatcherInterface; 
use App\Shared\Infrastructure\EventDispatcher\EventDispatcher;
use App\Shared\Application\Port\TransactionManagerInterface; 
use App\Shared\Infrastructure\Persistence\TransactionManager;

use App\Shared\Application\Port\CookieManagerInterface;
use App\Shared\Infrastructure\Http\CookieManager;

use App\Shared\Application\Port\MailerInterface;
use App\Shared\Infrastructure\Mailer\SmtpMailer;

use App\Shared\Infrastructure\Http\Request;
use App\Shared\Infrastructure\Bootstrap\EnvironmentLoader;


final class SharedContainerConfig {
    private function __construct() {}

    public static function register(Container $c):void{

        $classToInstance = [
            EventDispatcherInterface::class => EventDispatcher::class,
            TransactionManagerInterface::class => TransactionManager::class,
            CookieManagerInterface::class => CookieManager::class,
            
            Request::class  =>  function () { return Request::create(); },
            
            \PDO::class => function(){ 
                return new \PDO(
                    'mysql:host='.EnvironmentLoader::envOrFail('DB_HOST').';dbname='.EnvironmentLoader::envOrFail('DB_NAME').';charset=utf8mb4',
                    EnvironmentLoader::envOrFail('DB_USER'),
                    EnvironmentLoader::envOrFail('DB_PASSWORD')
                );
            },
            MailerInterface::class => function(){
                return new SmtpMailer(
                    EnvironmentLoader::envOrFail("SMTP_HOST"),
                    EnvironmentLoader::envOrFail("SMTP_USERNAME"),
                    EnvironmentLoader::envOrFail("SMTP_PASSWORD"),
                    (int)EnvironmentLoader::envOrFail("SMTP_PORT"),
                );
            },

        ];

        foreach($classToInstance as $key => $value){
            $c->bind($key,$value);
        }

    }

}
<?php

namespace App\Shared\Infrastructure\Di;
use App\Shared\Infrastructure\Di\Container;
use App\Shared\Infrastructure\Di\SharedContainerConfig;
use App\Auth\Infrastructure\Di\AuthContainerConfig;


final class ContainerConfig {
    private function __construct() {}

    public static function create():Container{
        $container = new Container();
        SharedContainerConfig::register($container);
        AuthContainerConfig::register($container);
        return $container;
    }

}
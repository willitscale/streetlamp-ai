<?php

use willitscale\Streetlamp\Builders\RouterConfigBuilder;
use willitscale\Streetlamp\RouteBuilder;
use willitscale\Streetlamp\Router;

require_once 'vendor/autoload.php';

if (!function_exists('dd')) {
    function dd(...$args): void
    {
        foreach ($args as $arg) {
            var_dump($arg);
        }
        exit(1);
    }
}

$routerConfig = new RouterConfigBuilder()
    ->setRouteCached(false)
    ->setRethrowExceptions(false)
    ->build();
$routeBuilder = new RouteBuilder($routerConfig);
$router = new Router($routeBuilder);
$router->renderRoute();

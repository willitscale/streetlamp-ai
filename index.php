<?php

declare(strict_types=1);

use willitscale\Streetlamp\Builders\RouteBuilder;
use willitscale\Streetlamp\Builders\RouterConfigBuilder;
use willitscale\Streetlamp\Router;

require_once __DIR__ . '/vendor/autoload.php';

function dd(...$args): void
{
    dump(...$args);
    exit(1);
}

function dump(...$args): void
{
    foreach ($args as $arg) {
        var_dump($arg);
    }
}

//error_log("Headers:" . json_encode(getallheaders(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
//error_log("Request:" . json_encode($_REQUEST, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
//error_log("Body: " . json_encode(file_get_contents('php://input'), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

$config = new RouterConfigBuilder()
    ->setRethrowExceptions(true)
    ->build();

$routeBuilder = new RouteBuilder($config);

try {
    new Router($routeBuilder)->render();
} catch (Throwable $e) {
    error_log($e->getMessage());
    dd($e->getMessage());
}

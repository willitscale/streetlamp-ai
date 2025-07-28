<?php

declare(strict_types=1);

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

try {
    new Router()->render();
} catch (Throwable $e) {
    dd($e->getMessage());
}

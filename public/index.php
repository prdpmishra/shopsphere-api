<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Container\Container;
use App\Routes\Router;
use App\Controllers\ProductController;

$container = new Container();

$router = $container->make(Router::class);

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$router->get('/products', ProductController::class, 'index');
$router->get('/products/{id}', ProductController::class, 'show');

$router->dispatch($_SERVER['REQUEST_METHOD'], $uri);

<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Routes\Router;
use App\Controllers\ProductController;

$router = new Router();

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$router->get('/products', ProductController::class, 'index');
$router->get('/products/{id}', ProductController::class, 'show');

$router->dispatch($_SERVER['REQUEST_METHOD'], $uri);

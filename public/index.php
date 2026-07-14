<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Routes\Router;

$router = new Router();

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$router->dispatch($uri);
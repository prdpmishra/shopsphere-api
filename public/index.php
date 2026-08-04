<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Container\Container;
use App\Routes\Router;
use App\Controllers\ProductController;
use App\Exceptions\ValidationException;
use App\Http\Request;
use App\Http\Response;

$container = new Container();

$router = $container->make(Router::class);

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$router->get('/products', ProductController::class, 'index');
$router->get('/products/{id}', ProductController::class, 'show');
$router->post('/products', ProductController::class, 'store');
$router->put('/products/{id}', ProductController::class, 'update');
$router->delete('/products/{id}', ProductController::class, 'delete');

$request = new Request();

try {
    $router->dispatch($request);
} catch (ValidationException $e) {
    Response::json([
        'message' => $e->getMessage(),
        'errors' => $e->errors()
    ], 422);
} catch (\Throwable $e) {
    Response::json([
        'message' => 'Internal Server Error'
    ], 500);
}

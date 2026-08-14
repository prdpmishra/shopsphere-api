<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Container\Container;
use App\Routes\Router;
use App\Controllers\ProductController;
use App\Exceptions\ValidationException;
use App\Http\Request;
use App\Http\Response;
use App\Middleware\AuthMiddleware;
use App\Middleware\ValidateRequest;

$container = new Container();

$router = $container->make(Router::class);

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$router->get('/products', ProductController::class, 'index', [ValidateRequest::class, AuthMiddleware::class]);
$router->get('/products/{id}', ProductController::class, 'show', [ValidateRequest::class, AuthMiddleware::class]);
$router->post('/products', ProductController::class, 'store', [ValidateRequest::class, AuthMiddleware::class]);
$router->put('/products/{id}', ProductController::class, 'update', [ValidateRequest::class, AuthMiddleware::class]);
$router->delete('/products/{id}', ProductController::class, 'delete', [ValidateRequest::class, AuthMiddleware::class]);

$request = new Request();

try {
    $router->dispatch($request);
} catch (ValidationException $e) {
    Response::json([
        'message' => $e->getMessage(),
        'errors' => $e->errors()
    ], 422);
} catch (\Throwable $e) {
    // Response::json([
    //     'message' => 'Internal Server Error'
    // ], 500);

    Response::json([
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
    ], 500);
}

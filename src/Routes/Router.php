<?php

declare(strict_types=1);

namespace App\Routes;

use App\Controllers\ProductController;

class Router
{
    public function dispatch(string $uri): void
    {
        if ($uri === '/products') {
            $controller = new ProductController();

            $controller->index();

            return;
        }

        http_response_code(404);

        echo "404 Not Found!";
    }
}
<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\ProductService;

class ProductController
{
    public function __construct(private ProductService $service)
    {

    }

    public function index(): void
    {
        header('Content-Type: application/json');

        echo json_encode(
            $this->service->getProducts()
        );
    }

    public function  show(int $id): void
    {
        header('Content-Type: application/json');

        $product = $this->service->getProduct($id);

        if ($product === null) {
            http_response_code(404);

            echo json_encode([
                'message' => 'Product not found!'
            ]);

            return;
        }

        echo json_encode($product);
    }
}

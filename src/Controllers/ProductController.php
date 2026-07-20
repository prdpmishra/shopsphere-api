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

        echo json_encode([
            'id' => $id,
            'message' => "Showing product {$id}"
        ]);
    }
}

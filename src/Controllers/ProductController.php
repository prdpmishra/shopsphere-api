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

    public function store(): void
    {
        header('Content-Type: application/json');

        $valid = true;

        $data = json_decode(file_get_contents('php://input'), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $valid = false;
        } elseif (!isset($data['name'], $data['quantity'], $data['price'])) {
            $valid = false;
        } elseif (!is_int($data['quantity']) || !is_int($data['price']) || !is_float($data['price'])) {
            $valid = false;
        }

        if (!$valid) {
            http_response_code(400);

            echo json_encode([
                'message' => 'Invalid request data!'
            ]);

            return;
        }

        $this->service->createProduct($data);

        http_response_code(201);

        echo json_encode([
            'message' => 'Product created successfully!'
        ]);
    }
}

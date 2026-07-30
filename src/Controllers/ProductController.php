<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Services\ProductService;

class ProductController
{
    public function __construct(private ProductService $service)
    {

    }

    public function index(): void
    {
        Response::json($this->service->getProducts());
    }

    public function show(int $id): void
    {
        $product = $this->service->getProduct($id);

        if ($product === null) {
            Response::json(['message' => 'Product not found!'], 404);

            return;
        }

        Response::json($product);
    }

    public function store(Request $request): void
    {
        $valid = true;

        $data = $request->all();

        if (!isset($data['name'], $data['quantity'], $data['price'])) {
            $valid = false;
        } elseif (!is_int($data['quantity']) || !(is_int($data['price']) || is_float($data['price']))) {
            $valid = false;
        }

        if (!$valid) {
            Response::json(['message' => 'Invalid request data!']);

            return;
        }

        $this->service->createProduct($data);

        Response::json(['message' => 'Product created successfully!'], 201);
    }

    public function update(Request $request, int $id): void
    {
        $data = $request->all();

        if (json_last_error() !== JSON_ERROR_NONE) {
            Response::json(['message' => 'Invalid request data!'], 400);

            return;
        } elseif (!isset($data['name'], $data['quantity'], $data['price']) || !is_string($data['name']) || trim($data['name']) === '') {
            Response::json(['message' => 'Invalid request data!'], 400);

            return;
        } elseif (!is_int($data['quantity']) || !(is_int($data['price']) || is_float($data['price']))) {
            Response::json(['message' => 'Invalid request data!'], 400);

            return;
        }

        $response = $this->service->updateProduct($id, $data);

        if ($response) {
            $message = 'Product updated successfully!';
        } else {
            $message = 'Nothing to update!';
        }

        Response::json(['message' => $message]);
    }

    public function delete(int $id): void
    {
        $this->service->deleteProduct($id);

        Response::json(['message' => 'Product deleted successfully!']);
    }
}

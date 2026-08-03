<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Services\ProductService;
use App\Validation\Validator;

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
        $data = $request->all();

        $validator = new Validator($data);

        $validator->validate([
            'name' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:1'],
            'quantity' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        if ($validator->fails()) {
            Response::json([
                'errors' => $validator->errors()
            ], 422);

            return;
        }

        $this->service->createProduct($data);

        Response::json(['message' => 'Product created successfully!'], 201);
    }

    public function update(Request $request, int $id): void
    {
        $data = $request->all();

        $validator = new Validator($data);

        $validator->validate([
            'name' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:1'],
            'quantity' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        if ($validator->fails()) {
            Response::json([
                'errors' => $validator->errors()
            ], 422);

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

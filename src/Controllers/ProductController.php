<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Http\Response;
use App\Requests\StoreProductRequest;
use App\Requests\UpdateProductRequest;
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

    public function store(StoreProductRequest $request): void
    {
        $data = $request->validated();

        $this->service->createProduct($data);

        Response::json(['message' => 'Product created successfully!'], 201);
    }

    public function update(UpdateProductRequest $request, int $id): void
    {
        $data = $request->validated();

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

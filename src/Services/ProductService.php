<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\ProductRepository;

class ProductService
{
    public function __construct(private ProductRepository $repository)
    {

    }

    public function getProducts(): array
    {
        return $this->repository->all();
    }
}

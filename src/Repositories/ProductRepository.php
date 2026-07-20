<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Database\DatabaseConnection;

class ProductRepository
{
    public function __construct(private DatabaseConnection $database)
    {

    }

    public function all(): array
    {
        $products = $this->database->getConnection()->query("SELECT * FROM products");

        return $products->rowCount() > 0 ? $products->fetchAll() : [];
    }
}

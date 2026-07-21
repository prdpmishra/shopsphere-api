<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Database\DatabaseConnection;
use PDO;

class ProductRepository
{
    private PDO $database;

    public function __construct(DatabaseConnection $database)
    {
        $this->database = $database->getConnection();
    }

    public function all(): array
    {
        $products = $this->database->query("SELECT * FROM products");

        return $products->rowCount() > 0 ? $products->fetchAll() : [];
    }

    public function find(int $id): ?array
    {
        $statement = $this->database->prepare("SELECT * FROM products WHERE id = :id");

        $statement->execute([
            "id" => $id
        ]);

        return $statement->rowCount() > 0 ? $statement->fetch() : null;
    }
}

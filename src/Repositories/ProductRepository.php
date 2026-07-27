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

    public function create(array $data): bool
    {
        $statement = $this->database->prepare("INSERT INTO products (name, price, quantity) VALUES (:name, :price, :quantity)");

        $statement->execute([
            "name" => $data['name'],
            "price" => $data['price'],
            "quantity" => $data['quantity'],
        ]);

        return true;
    }

    public function update(int $id, array $data): bool
    {
        $statement = $this->database->prepare("UPDATE products SET name = :name, price = :price, quantity = :quantity WHERE id = :id");

        $statement->execute([
            "name" => $data['name'],
            "price" => $data['price'],
            "quantity" => $data['quantity'],
            "id" => $id,
        ]);

        return $statement->rowCount() > 0;
    }

    public function delete(int $id): bool
    {
        $statement = $this->database->prepare("DELETE FROM products WHERE id = :id");

        $statement->execute([
            "id" => $id
        ]);

        return true;
    }
}

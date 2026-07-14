<?php

declare(strict_types=1);

namespace App\Controllers;

class ProductController
{
    public function index(): void
    {
        header('Content-Type: application/json');

        echo json_encode([
            'message' => 'Products API is working!'
        ]);
    }
}
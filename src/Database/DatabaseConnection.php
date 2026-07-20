<?php

declare(strict_types=1);

namespace App\Database;

use PDO;

class DatabaseConnection
{
    private PDO $pdo;

    public function __construct()
    {
        // 1. Connection Configurations
        $host    = 'localhost';
        $db      = 'shopsphere';
        $user    = 'root';
        $pass    = '';
        $charset = 'utf8mb4';   // Crucial for supporting modern characters and emojis

        // 2. Data Source Name (DSN)
        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

        // 3. Essential Professional Options
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throws exceptions on SQL errors
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Returns clean associative arrays
            PDO::ATTR_EMULATE_PREPARES   => false,                  // Uses native prepared statements for security
        ];

        try {
            // 4. Establish Connection
            $this->pdo = new PDO($dsn, $user, $pass, $options);
        } catch (\PDOException $e) {
            // 5. Handle Failures Securely
            // Log the error message to the server log instead of echoing it to users (prevents data leaks)
            error_log($e->getMessage());
            exit('Database connection failed.');
        }
    }

    public function getConnection(): PDO
    {
        return $this->pdo;
    }
}

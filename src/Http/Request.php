<?php

declare(strict_types=1);

namespace App\Http;

use App\Exceptions\InvalidRequestException;

class Request
{
    private ?array $json = null;

    public function method(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function uri(): string
    {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }

    public function json(): array
    {
        if ($this->json !== null) {
            return $this->json;
        }

        $this->json = json_decode(file_get_contents('php://input'), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidRequestException('Invalid JSON.');
        }

        return $this->json;
    }

    public function input(string $key, mixed $default = null) :mixed
    {
        $data = $this->json();

        return $data[$key] ?? $default;
    }

    public function all(): array
    {
        return $this->json();
    }

    public function header(string $key, mixed $default = null): mixed
    {
        $key = strtoupper(str_replace('-', '_', $key));

        $server_key = 'HTTP_' . $key;

        return $_SERVER[$server_key] ?? $default;
    }
}

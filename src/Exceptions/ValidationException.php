<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class ValidationException extends Exception
{
    public function __construct(
        private array $errors,
        string $message = 'The given data was invalid.'
    )
    {
        parent::__construct($message);
    }

    public function errors(): array
    {
        return $this->errors;
    }
}

<?php

declare(strict_types=1);

namespace App\Validation;

class Validator
{
    private array $errors = [];

    public function __construct(private array $data)
    {

    }

    public function validate(array $fields): void
    {
        foreach ($fields as $field => $rules) {
            foreach ($rules as $rule) {
                $parts = explode(':', $rule, 2);

                $method = $parts[0];
                $argument = $parts[1] ?? null;

                if (!method_exists($this, $method)) {
                    throw new \Exception("Validation rule '{$method}' does not exist.");
                }

                if ($argument === null) {
                    $this->{$method}($field);
                } else {
                    $this->{$method}($field, $argument);
                }
            }
        }
    }

    public function required(string $field): self
    {
        $value = $this->value($field);

        if ($value === null || $value === '') {
            $this->addError($field, "The {$field} field is required.");
        }

        return $this;
    }

    public function string(string $field): self
    {
        $value = $this->value($field);

        if (array_key_exists($field, $this->data) && !is_string($value)) {
            $this->addError($field, "The {$field} field must be a string.");
        }

        return $this;
    }

    public function integer(string $field): self
    {
        $value = $this->value($field);

        if (array_key_exists($field, $this->data) && !is_integer($value)) {
            $this->addError($field, "The {$field} field must be an integer.");
        }

        return $this;
    }

    public function numeric(string $field): self
    {
        $value = $this->value($field);

        if (array_key_exists($field, $this->data) && !is_numeric($value)) {
            $this->addError($field, "The {$field} field must be numeric.");
        }

        return $this;
    }

    public function min(string $field, string $min): self
    {
        $value = $this->value($field);

        if ($value === null || $value === '') {
            return $this;
        }

        if ($value < (int)$min) {
            $this->addError($field, "The {$field} field must be at least {$min}.");
        }

        return $this;
    }

    public function max(string $field, string $max): self
    {
        $value = $this->value($field);

        if ($value === null || $value === '') {
            return $this;
        }

        if ($value > (int)$max) {
            $this->addError($field, "The {$field} field must not exceed {$max}.");
        }

        return $this;
    }

    public function fails(): bool
    {
        return !empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    private function value(string $field): mixed
    {
        return $this->data[$field] ?? null;
    }

    private function addError(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }
}

<?php

declare(strict_types=1);

namespace App\Container;

class Container
{
    private array $bindings = [];

    public function bind(string $abstract, string $concrete): void
    {
        $this->bindings[$abstract] = $concrete;
    }

    public function make(string $abstract): object
    {
        if (!isset($this->bindings[$abstract])) {
            $concrete = $abstract;
        } else {
            $concrete = $this->bindings[$abstract];
        }

        $reflection = new \ReflectionClass($concrete);

        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            return new $concrete();
        }

        $dependencies = [];

        foreach ($constructor->getParameters() as $parameter) {
            $type = $parameter->getType();

            $dependencies[] = $this->make($type->getName());
        }

        return $reflection->newInstanceArgs($dependencies);
    }
}

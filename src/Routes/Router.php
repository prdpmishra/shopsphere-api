<?php

declare(strict_types=1);

namespace App\Routes;

use App\Controllers\ProductController;
use App\Container\Container;

class Router
{
    private array $routes = [];

    public function __construct(private Container $container)
    {

    }

    public function get(string $uri, string $controller, string $action): void
    {
        if (isset($this->routes['GET'][$uri])) {
            throw new \Exception("Route already registered");
        }

        $this->routes['GET'][$uri] = [
            'controller' => $controller,
            'action' => $action
        ];
    }

    private function findRoute(string $method, string $uri): ?array
    {
        $parameters = [];

        $uriParts = explode('/', $uri);

        foreach ($this->routes[$method] as $key => $route) {
            $matched = true;
            $routeParts = explode('/', $key);

            if (count($routeParts) == count($uriParts)) {
                foreach ($routeParts as $index => $routePart) {
                    $uriPart = $uriParts[$index];

                    if ($this->isParameter($routePart)) {
                        $name = trim($routePart, '{}');

                        $parameters[$name] = $uriPart;

                        continue;
                    }

                    if ($uriPart !== $routePart) {
                        $matched = false;

                        break;
                    }
                }

                if ($matched && isset($this->routes[$method][$key])) {
                    return [
                        'route' => $this->routes[$method][$key],
                        'parameters' => $parameters
                    ];
                }
            }
        }

        return null;
    }

    private function notFound(): void
    {
        http_response_code(404);

        echo json_encode([
            'message' => 'Not Found'
        ]);
    }

    private function isParameter(string $routePart): bool
    {
        return str_starts_with($routePart, '{') && str_ends_with($routePart, '}');
    }

    private function execute(array $route, array $parameters): void
    {
        $action = $route['action'];

        $controller = $this->container->make($route['controller']);

        if (!method_exists($controller, $action)) {
            throw new \Exception("Method {$action} does not exist.");
        }

        $controller->$action(...array_map('intval', array_values($parameters)));
    }

    public function dispatch(string $method, string $uri): void
    {
        $method = strtoupper($method);

        $result = $this->findRoute($method, $uri);

        if ($result === null) {
            $this->notFound();

            return;
        }

        $this->execute($result['route'], $result['parameters']);
    }
}

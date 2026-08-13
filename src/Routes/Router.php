<?php

declare(strict_types=1);

namespace App\Routes;

use App\Controllers\ProductController;
use App\Container\Container;
use App\Http\Request;
use App\Middleware\MiddlewareInterface;
use Closure;
use ReflectionMethod;
use ReflectionNamedType;

class Router
{
    private array $routes = [];

    public function __construct(private Container $container)
    {

    }

    private function addRoute(string $method, string $uri, string $controller, string $action, array $middleware = []): void
    {
        if (isset($this->routes[$method][$uri])) {
            throw new \Exception("Route already registered");
        }

        $this->routes[$method][$uri] = [
            'controller' => $controller,
            'action' => $action,
            'middleware' => $middleware
        ];
    }

    public function get(string $uri, string $controller, string $action, array $middleware = []): void
    {
        $this->addRoute('GET', $uri, $controller, $action, $middleware);
    }

    public function post(string $uri, string $controller, string $action, array $middleware = []): void
    {
        $this->addRoute('POST', $uri, $controller, $action, $middleware);
    }

    public function put(string $uri, string $controller, string $action, array $middleware = []): void
    {
        $this->addRoute('PUT', $uri, $controller, $action, $middleware);
    }

    public function delete(string $uri, string $controller, string $action, array $middleware = []): void
    {
        $this->addRoute('DELETE', $uri, $controller, $action, $middleware);
    }

    private function findRoute(string $method, string $uri): ?array
    {
        if (!isset($this->routes[$method])) {
            return null;
        }

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

    private function execute(array $route, array $parameters, Request $request): void
    {
        $action = $route['action'];

        $controller = $this->container->make($route['controller']);

        if (!method_exists($controller, $action)) {
            throw new \Exception("Method {$action} does not exist.");
        }

        $args = [];

        $reflection = new ReflectionMethod($controller, $action);

        foreach ($reflection->getParameters() as $parameter) {
            $name = $parameter->getName();
            $type = $parameter->getType();

            if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
                $args[] = $this->container->make($type->getName());

                continue;
            }

            $type = $parameter->getType()?->getName();

            if ($type !== null && class_exists($type)) {
                $args[] = $this->container->make($type);

                continue;
            }

            if (!array_key_exists($name, $parameters)) {
                if ($parameter->isDefaultValueAvailable()) {
                    $args[] = $parameter->getDefaultValue();

                    continue;
                }

                throw new \Exception(
                    "Missing route parameter '{$name}'."
                );
            }

            $value = $parameters[$name];

            $args[] = match ($type) {
                'int' => (int) $value,
                'float' => (float) $value,
                default => $value
            };
        }

        $destination = function (Request $request) use ($controller, $action, $args): void {
            $controller->$action(...$args);
        };

        $this->runMiddleware($route['middleware'], $request, $destination);
    }

    public function dispatch(Request $request): void
    {
        $method = strtoupper($request->method());
        $uri = $request->uri();

        $result = $this->findRoute($method, $uri);

        if ($result === null) {
            $this->notFound();

            return;
        }

        $this->execute($result['route'], $result['parameters'], $request);
    }

    private function runMiddleware(array $middleware, Request $request, Closure $destination): void {
        if (empty($middleware)) {
            $destination($request);

            return;
        }

        $middlewareClass = array_shift($middleware);

        $instance = $this->container->make($middlewareClass);

        if (!$instance instanceof MiddlewareInterface) {
            throw new \Exception(
                "{$middlewareClass} must implement MiddlewareInterface."
            );
        }

        $next = function (Request $request) use ($middleware, $destination): void {
            $this->runMiddleware($middleware, $request, $destination);
        };

        $instance->handle($request, $next);
    }
}

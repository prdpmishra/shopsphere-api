<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Http\Request;
use App\Http\Response;
use Closure;

class AuthMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Closure $next): void
    {
        if (!$this->isAuthenticated($request)) {
            Response::json([
                'message' => 'Unauthenticated.'
            ], 401);

            return;
        }

        $next($request);
    }

    private function isAuthenticated(Request $request): bool
    {
        $auth_header = $request->header('Authorization');

        if (!is_string($auth_header) || !str_starts_with($auth_header, 'Bearer ')) {
            return false;
        }

        $auth_token = substr($auth_header, 7);

        return $auth_token === 'valid-token';
    }
}

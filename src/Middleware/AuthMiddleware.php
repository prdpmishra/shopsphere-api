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
        }

        $next($request);
    }

    private function isAuthenticated($request): bool
    {
        return true;
    }
}

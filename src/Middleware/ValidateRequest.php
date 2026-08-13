<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Http\Request;
use Closure;

class ValidateRequest implements MiddlewareInterface
{
    public function handle(Request $request, Closure $next): void
    {
        $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! auth()->check() || ! auth()->user()->hasAnyRole($roles)) {
            abort(403, 'Acesso não autorizado.');
        }

        return $next($request);
    }
}

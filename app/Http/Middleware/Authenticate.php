<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Closure;

class Authenticate extends Middleware
{
    protected function redirectTo($request)
    {
        // Devolver JSON en vez de redirigir
        if (! $request->expectsJson()) {
            abort(response()->json(['error' => 'No autorizado'], 401));
        }
    }

    public function handle($request, Closure $next, ...$guards)
    {
        try {
            $this->authenticate($request, $guards);
        } catch (\Illuminate\Auth\AuthenticationException $e) {
            return response()->json(['error' => 'No autorizado'], 401);
        }

        return $next($request);
    }
}

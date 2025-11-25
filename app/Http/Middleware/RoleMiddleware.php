<?php

namespace App\Http\Middleware;

use Closure;

class RoleMiddleware
{
    public function handle($request, Closure $next, ...$roles)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'No autenticado'
            ], 401);
        }


        $userRole = strtolower($user->role);
        $roles = array_map('strtolower', $roles);

        if (!in_array($userRole, $roles)) {
            return response()->json([
                'message' => 'Acceso denegado. Rol requerido: ' . implode(' o ', $roles)
            ], 403);
        }

        return $next($request);
    }
}

<?php

use Laravel\Sanctum\Sanctum;

return [

    // No usamos cookies ni sesiones → vacío
    'stateful' => [],

    // No usamos el guard web → desactivado
    'guard' => [],

    // Los tokens no expiran (o añade minutos si quieres caducidad)
    'expiration' => null,

    // Prefijo opcional
    'token_prefix' => env('SANCTUM_TOKEN_PREFIX', ''),

    // Sin middlewares stateful
    'middleware' => [
        // Modo API puro, sin CSRF, sin cookies
    ],
];

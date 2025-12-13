<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SolicitudGastoController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\AlertaController;
use App\Http\Controllers\Api\DashboardController;

/*RUTAS PÚBLICAS*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// Para evitar error si se accede por GET a /login
Route::get('/login', function () {
    return response()->json([
        'message' => 'Use método POST para iniciar sesión.'
    ], 405);
});

/*RUTAS PROTEGIDAS (auth:sanctum)*/

Route::middleware('auth:sanctum')->group(function () {

    // Usuario autenticado
    Route::get('/me',      [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    /*ACCIONES ACCESIBLES A TODOS LOS ROLES AUTENTICADOS*/

    // Dashboard
    Route::get('/dashboard/resumen', [DashboardController::class, 'resumen']);
    Route::get('/dashboard', fn() => response()->json(['ok' => true]));

    // Solicitudes (ver + listar)
    Route::get('/solicitudes',      [SolicitudGastoController::class, 'index']);
    Route::get('/solicitudes/{id}', [SolicitudGastoController::class, 'show']);
    Route::post('/solicitudes', [SolicitudGastoController::class, 'store']);

    // Reportes
    Route::get('/reportes', [ReportController::class, 'index']);

    /*RUTAS ACCESIBLES SOLO PARA AUDITOR + ADMIN*/

    Route::middleware('role:auditor,admin')->group(function () {

        // Validación de solicitudes
        Route::post('/solicitudes/validar',          [SolicitudGastoController::class, 'validarMasivo']);
        Route::post('/solicitudes/rechazar-masivo',  [SolicitudGastoController::class, 'rechazarMasivo']);
        Route::put('/solicitudes/{id}',              [SolicitudGastoController::class, 'update']);

        // Exportaciones
        Route::get('/reportes/pdf',   [ReportController::class, 'exportPdf']);
        Route::get('/reportes/excel', [ReportController::class, 'exportExcel']);

        // Alertas
        Route::get('/alertas',                  [AlertaController::class, 'index']);
        Route::post('/alertas/check',           [AlertaController::class, 'checkByProveedor']);
        Route::patch('/alertas/{id}/estado',    [AlertaController::class, 'updateEstado']);
        Route::post('/alertas/generar',         [AlertaController::class, 'generarAlertas']); 
    });

    /*RUTAS SOLO PARA ADMIN*/

    Route::middleware('role:admin')->group(function () {

        // Users
        Route::get('/users',    [UserController::class, 'index']);
        Route::get('/usuarios', [UserController::class, 'index']);

        Route::prefix('users')->group(function () {
            Route::post('/',    [UserController::class, 'store']);
            Route::put('/{id}', [UserController::class, 'update']);
            Route::delete('/{id}', [UserController::class, 'destroy']);
        });

        Route::prefix('usuarios')->group(function () {
            Route::post('/',       [UserController::class, 'store']);
            Route::put('/{id}',    [UserController::class, 'update']);
            Route::delete('/{id}', [UserController::class, 'destroy']);
        });
    });

});

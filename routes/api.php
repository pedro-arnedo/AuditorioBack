<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RolController;
use App\Http\Controllers\Api\UsuarioController;
use App\Http\Middleware\TokenMiddleware;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\InstitucionController;

Route::prefix('v1')->group(function () {

    // ROLES
    Route::get('/roles', [RolController::class, 'index']);
    Route::get('/roles/{id}', [RolController::class, 'show']);

    // AUTH
    Route::post('/auth/login', [AuthController::class, 'login']);

    Route::post('/auth/logout', [AuthController::class, 'logout'])
        ->middleware(TokenMiddleware::class);

    // RUTAS QUE REQUIEREN TOKEN
    Route::middleware(TokenMiddleware::class)->group(function () {

        // ROLES
        Route::get('/roles', [RolController::class, 'index']);
        Route::get('/roles/{id}', [RolController::class, 'show']);

        // USUARIOS
        Route::get('/usuarios', [UsuarioController::class, 'index']);
        Route::post('/usuarios', [UsuarioController::class, 'store']);
        Route::get('/usuarios/{id}', [UsuarioController::class, 'show']);
        Route::put('/usuarios/{id}', [UsuarioController::class, 'update']);
        Route::patch('/usuarios/{id}/estado', [UsuarioController::class, 'cambiarEstado']);
        Route::post('/usuarios/{id}/restablecer-password', [UsuarioController::class, 'restablecerPassword']);

        // HU-001 - INSTITUCIONES
        Route::get('/instituciones/validar-codigo', [InstitucionController::class, 'validarCodigo']);
        Route::post('/instituciones', [InstitucionController::class, 'store']);
        Route::get('/instituciones', [InstitucionController::class, 'index']);
        Route::get('/instituciones/{id}', [InstitucionController::class, 'show']);
        Route::put('/instituciones/{id}', [InstitucionController::class, 'update']);
    });
});

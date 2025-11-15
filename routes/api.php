<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Middleware\TokenMiddleware;
use App\Http\Controllers\Api\InstitucionController;

Route::prefix('v1')->group(function () {

    // AUTH
    Route::post('/auth/login', [AuthController::class, 'login']);

    Route::post('/auth/logout', [AuthController::class, 'logout'])
        ->middleware(TokenMiddleware::class);

    // RUTAS QUE REQUIEREN TOKEN
    Route::middleware(TokenMiddleware::class)->group(function () {

        // HU-001 - INSTITUCIONES
        Route::get('/instituciones/validar-codigo', [InstitucionController::class, 'validarCodigo']);
        Route::post('/instituciones', [InstitucionController::class, 'store']);
        Route::get('/instituciones', [InstitucionController::class, 'index']);
        Route::get('/instituciones/{id}', [InstitucionController::class, 'show']);
        Route::put('/instituciones/{id}', [InstitucionController::class, 'update']);
    });
});

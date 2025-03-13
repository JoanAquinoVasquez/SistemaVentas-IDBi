<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\DetalleVentaController;
use Illuminate\Http\Request;

// Ruta de prueba para obtener el usuario autenticado
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

// Rutas públicas para autenticación
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');

// Grupo protegido por autenticación
Route::middleware(['auth:api'])->group(function () {

    // Rutas para ADMIN
    Route::middleware(['role:admin'])->group(function () {
        // El administrador puede hacer todo lo relacionado con productos y ventas
        Route::apiResource('products', ProductController::class); // Rutas completas para productos
        Route::apiResource('ventas', VentaController::class);
        Route::post('register', [AuthController::class, 'register']);
        Route::apiResource('detalle-venta', DetalleVentaController::class);
    });

    // Rutas para VENDEDOR
    Route::middleware(['role:vendedor|admin'])->group(function () {
        // El vendedor puede crear, listar y actualizar productos
        Route::apiResource('products', ProductController::class)->only(['index', 'store', 'update']);
        // El vendedor también puede registrar ventas
        Route::post('ventas', [VentaController::class, 'store']);
        Route::post('detalle-venta', [DetalleVentaController::class, 'store']);
    });
});

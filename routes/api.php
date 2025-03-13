<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\VentaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReporteVentaController;

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
        Route::apiResource('ventas', VentaController::class);     // Rutas completas para ventas
        Route::post('register', [AuthController::class, 'register']);
        Route::get('reporte-ventas/json', [ReporteVentaController::class, 'reporteJson']);
        Route::get('reporte-ventas/xlsx', [ReporteVentaController::class, 'reporteXlsx']);
    });

    // Rutas para VENDEDOR
    Route::middleware(['role:vendedor|admin'])->group(function () {
        // El vendedor puede crear, listar y actualizar productos
        Route::apiResource('products', ProductController::class)->only(['index', 'store', 'update']);
        // El vendedor también puede registrar ventas
        Route::apiResource('ventas', VentaController::class)->only(['store']);
    });
});

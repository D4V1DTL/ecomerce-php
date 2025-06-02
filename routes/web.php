<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;

// Rutas públicas (sin token)
Route::post('register', [AuthController::class, 'register']);
Route::post('login',    [AuthController::class, 'login']);

// Rutas protegidas (con token JWT)
Route::middleware('auth:api')->group(function () {
    // Usuario autenticado
    Route::get('me',     [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);

    // Productos - CRUD completo con apiResource
    Route::apiResource('products', ProductController::class);

    // Carrito - rutas manuales para mejor control
    Route::get('cart', [CartController::class, 'index']);                     // Listar productos en carrito
    Route::post('cart/add', [CartController::class, 'add']);                  // Agregar producto al carrito
    Route::delete('cart/remove/{productId}', [CartController::class, 'remove']); // Eliminar producto del carrito

    // Pedidos - checkout básico
    Route::post('checkout', [OrderController::class, 'store']);
    Route::patch('orders/{orderId}/status', [OrderController::class, 'updateStatus']);
});

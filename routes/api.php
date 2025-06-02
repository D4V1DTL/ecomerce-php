<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CategoryController;

// Rutas públicas (sin token)
Route::post('register', [AuthController::class, 'register']);
Route::post('login',    [AuthController::class, 'login']);

// Solo index público
Route::get('categories', [CategoryController::class, 'index']);
Route::get('products', [ProductController::class, 'index']);
Route::get('products/{id}', [ProductController::class, 'show']);

// Rutas protegidas (con token JWT)
Route::middleware('auth:api')->group(function () {
    // Usuario autenticado
    Route::get('me',     [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);

    // CRUD protegido para categorías y productos (excepto index)
    Route::apiResource('categories', CategoryController::class)->except(['index']);
    Route::apiResource('products', ProductController::class)->except(['index']);

    // Carrito
    Route::get('cart', [CartController::class, 'index']);
    Route::post('cart/add', [CartController::class, 'add']);
    Route::post('cart/sync', [CartController::class, 'sincronizar']);
    Route::delete('cart/clear', [CartController::class, 'clear']);
    Route::delete('cart/remove/{productId}', [CartController::class, 'remove']);

    // Pedidos
    Route::post('checkout', [OrderController::class, 'store']);
    // Listar pedidos del usuario autenticado
    Route::get('orders', [OrderController::class, 'index']);
    // Ver detalle de un pedido
    Route::get('orders/{id}', [OrderController::class, 'show']);
    Route::patch('orders/{orderId}/status', [OrderController::class, 'updateStatus']);
});

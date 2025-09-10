<?php

use Illuminate\Support\Facades\Route;           // ✅ the facade
use Illuminate\Session\Middleware\StartSession;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PricingController;     // remove if you don't have it

Route::middleware(['api', StartSession::class])->group(function () {
  // demo auth (session-backed, no DB)
  Route::get('/auth/me', [AuthController::class, 'me']);
  Route::post('/auth/login', [AuthController::class, 'login']);
  Route::post('/auth/logout', [AuthController::class, 'logout']);

  // cart
  Route::get('/cart', [CartController::class, 'show']);
  Route::post('/cart/items', [CartController::class, 'store']);
  Route::patch('/cart/items/{productId}', [CartController::class, 'update']);
  Route::delete('/cart/items/{productId}', [CartController::class, 'destroy']);
  Route::delete('/cart', [CartController::class, 'clear']);

  // products
  Route::get('/products', [ProductController::class, 'index']);

  // pricing (optional)
  Route::get('/pricing', [PricingController::class, 'show']);
  Route::middleware('role:admin')->put('/pricing', [PricingController::class, 'update']);
});

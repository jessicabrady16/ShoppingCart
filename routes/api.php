<?php

use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use App\Http\Controllers\CartController;
use Illuminate\Cookie\Middleware\EncryptCookies;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use Illuminate\Session\Middleware\StartSession;


Route::middleware(['api', EncryptCookies::class, AddQueuedCookiesToResponse::class, StartSession::class])->group(function () {
  Route::get('/cart', [CartController::class, 'show']);
  Route::post('/cart/items', [CartController::class, 'store']);
  Route::patch('/cart/items/{productId}', [CartController::class, 'update']);
  Route::delete('/cart/items/{productId}', [CartController::class, 'destroy']);
  Route::delete('/cart', [CartController::class, 'clear']);
  Route::get('/products', [ProductController::class, 'index']);
});

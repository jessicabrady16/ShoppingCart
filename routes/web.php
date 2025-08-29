<?php

use Illuminate\Support\Facades\Route;


Route::view('/', 'home');       // new home
Route::view('/shop', 'shop');   // shopper UI
Route::view('/cart', 'cart');   // cart UI (moved off '/')
Route::view('/api-docs', 'api-docs'); // (you already have this file or add later)

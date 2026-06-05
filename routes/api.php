<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\KitController;
use App\Http\Controllers\Api\CategoriaController;
use App\Http\Controllers\Api\ItemController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function() {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('user', [AuthController::class, 'user']);
    
    Route::get('/kits', [KitController::class, 'index']);
    Route::post('/kits', [KitController::class, 'store']);
    Route::get('/kits/{id}', [KitController::class, 'show']);
    Route::put('/kits/{id}', [KitController::class, 'update']);
    Route::delete('/kits/{id}', [KitController::class, 'destroy']);

    Route::get('/categorias', [CategoriaController::class, 'index']);
    Route::post('/categorias', [CategoriaController::class, 'store']);
    Route::put('/categorias/{id}', [CategoriaController::class, 'update']);
    Route::delete('/categorias/{id}', [CategoriaController::class, 'destroy']);

    Route::get('/items', [ItemController::class, 'index']);
    Route::post('/items', [ItemController::class, 'store']);
    Route::put('/items/{id}', [ItemController::class, 'update']);
    Route::delete('/items/{id}', [ItemController::class, 'destroy']);

    });
<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;

//
// 🔓 Public Routes
//
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

//
// 🔐 Protected Routes
//
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::post('/me', [AuthController::class, 'me']);

    // Product routes
    Route::get('/products', [ProductController::class, 'index']);       
    Route::post('/addproduct', [ProductController::class, 'store']);   

    // Order routes
    Route::get('/orders', [OrderController::class, 'index']);          
    Route::post('/orders', [OrderController::class, 'store']);         
    Route::delete('/orders/{id}', [OrderController::class, 'destroy']); 
    
});

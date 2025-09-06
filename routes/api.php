<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Chart;
use App\Http\Controllers\ConfirmController;
//
// 🔓 Public Routes
//
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here you can register API routes for your application. These routes
| are loaded by the RouteServiceProvider and assigned the "api" middleware group.
|
*/

// Example protected route for authenticated user
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Public authentication routes
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);


Route::get('/allproducts', [ProductController::class, 'allProducts'])->middleware('jwt.verify');
Route::post('/addproduct', [ProductController::class, 'store'])->middleware('jwt.verify');
// Group routes with API 
Route::get('/orders/manager', [OrderController::class, 'getAllOrders'])->middleware('jwt.verify');
Route::post('/orders', [OrderController::class, 'store'])->middleware('jwt.verify');
Route::post('/increment', [Chart::class, 'increment'])->middleware('jwt.verify');
Route::post('/decrement', [Chart::class, 'decrement'])->middleware('jwt.verify');

Route::post('/addchart', [Chart::class, 'addToCart'])->middleware('jwt.verify');
Route::put('/cart/increment/{cartId}', [Chart::class, 'increment'])->middleware('jwt.verify');
Route::put('/cart/decrement/{cartId}', [Chart::class, 'decrement'])->middleware('jwt.verify');
Route::get('/user-cart/{userId}', [Chart::class, 'getUserCart'])->middleware('jwt.verify');
Route::post('/checkout', [ConfirmController::class, 'checkout'])->middleware('jwt.verify');
Route::post('/orders/confirm/{id}', action: [ConfirmController::class, 'confirm'])->middleware('jwt.verify');
Route::put('/orders/{id}/confirmed-by', [OrderController::class, 'updateConfirmedBy'])->middleware('jwt.verify');


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::post('/me', [AuthController::class, 'me']);


    Route::get('/orders', [OrderController::class, 'index']);
    Route::delete('/orders/{id}', [OrderController::class, 'destroy']);

});

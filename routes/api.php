<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Chart;
<<<<<<< HEAD
use App\Http\Controllers\ConfirmController;
//
// 🔓 Public Routes
//
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
=======
use App\Http\Controllers\StripeController;
use App\Http\Controllers\StripeWebhookController;
>>>>>>> 1b88c3fa746ade60b6c7d550caf04430d98a56d4

/*
|--------------------------------------------------------------------------
| Public Routes (No JWT Required)
|--------------------------------------------------------------------------
*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Stripe webhook route (public)
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook']);

/*
|--------------------------------------------------------------------------
| Protected Routes (JWT Required)
|--------------------------------------------------------------------------
*/
Route::middleware(['jwt.verify'])->group(function () {

    // 🔐 Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);  // added as per your snippet
    Route::post('/me', [AuthController::class, 'me']);

    // 💳 Stripe Checkout
    Route::post('/create-checkout-session', [StripeController::class, 'createCheckoutSession']);

    // 📦 Products
    Route::get('/products', [ProductController::class, 'index']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);

    // Alternative product routes from your second snippet
    Route::get('/allproducts', [ProductController::class, 'allProducts']);
    Route::post('/addproduct', [ProductController::class, 'store']);

    // 📦 Orders
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::delete('/orders/{id}', [OrderController::class, 'destroy']);

    // 📊 Charts
    Route::get('/charts/data', [Chart::class, 'getData']);

    // Cart/Chart related routes
    Route::post('/addchart', [Chart::class, 'addToCart']);
    Route::post('/increment', [Chart::class, 'increment']);
    Route::post('/decrement', [Chart::class, 'decrement']);
    Route::put('/cart/increment/{cartId}', [Chart::class, 'increment']);
    Route::put('/cart/decrement/{cartId}', [Chart::class, 'decrement']);
    Route::get('/user-cart/{userId}', [Chart::class, 'getUserCart']);
});

// Optional: Sanctum authenticated user route example if needed
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
<<<<<<< HEAD

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
=======
>>>>>>> 1b88c3fa746ade60b6c7d550caf04430d98a56d4

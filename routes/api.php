<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Chart;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\CommentController; 

/*
|-------------------------------------------------------------------------- 
| Public Routes (No JWT Required) 
|-------------------------------------------------------------------------- 
*/
Route::post('/register', [AuthController::class, 'register']); 
Route::post('/login', [AuthController::class, 'login']); 


Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook']);

/*
|-------------------------------------------------------------------------- 
| Protected Routes (JWT Required) 
|-------------------------------------------------------------------------- 
*/
Route::middleware(['jwt.verify'])->group(function () {

  
    Route::post('/logout', [AuthController::class, 'logout']); 
    Route::post('/refresh', [AuthController::class, 'refresh']); 
    Route::post('/me', [AuthController::class, 'me']); 

    
    Route::post('/create-checkout-session', [StripeController::class, 'createCheckoutSession']); 

    
    Route::get('/products', [ProductController::class, 'index']); 
    Route::post('/products', [ProductController::class, 'store']); 
    Route::get('/products/{id}', [ProductController::class, 'show']); 
    Route::put('/products/{id}', [ProductController::class, 'update']); 
    Route::delete('/products/{id}', [ProductController::class, 'destroy']); 

    
    Route::get('/allproducts', [ProductController::class, 'allProducts']); 
    Route::post('/addproduct', [ProductController::class, 'store']); 

    
    Route::get('/orders', [OrderController::class, 'index']); 
    Route::post('/orders', [OrderController::class, 'store']); 
    Route::delete('/orders/{id}', [OrderController::class, 'destroy']); 

  
    Route::get('/charts/data', [Chart::class, 'getData']); 

   
    Route::post('/addchart', [Chart::class, 'addToCart']); 
    Route::post('/increment', [Chart::class, 'increment']); 
    Route::post('/decrement', [Chart::class, 'decrement']); 
    Route::put('/cart/increment/{cartId}', [Chart::class, 'increment']); 
    Route::put('/cart/decrement/{cartId}', [Chart::class, 'decrement']); 
    Route::get('/user-cart/{userId}', [Chart::class, 'getUserCart']); 

    
    Route::post('/comments', [CommentController::class, 'store']); 
    Route::get('/comments/{productId}', [CommentController::class, 'index']); 
    Route::delete('/comments/{id}', [CommentController::class, 'destroy']); 
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

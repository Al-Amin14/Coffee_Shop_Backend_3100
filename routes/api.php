<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Chart;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\StripeWebhookController;



Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook']);

Route::middleware('api')->group(function () {


Route::post('create-checkout-session', [StripeController::class, 'createCheckoutSession']);


//Route::post('stripe/webhook', [StripeWebhookController::class, 'handleWebhook']);




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

Route::post('/addproduct', [ProductController::class, 'store']);

// Group routes with API middleware
Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);
    
});

});

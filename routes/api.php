<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ContactUsController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ConfirmController;
use App\Http\Controllers\GenAiController;





Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/', function () {
    return view('welcome');
});
Route::get('/hello', function () {
    return response()->json(['error'=>'Message']);
});
/*
|-------------------------------------------------------------------------- 
| Protected Routes (JWT Required) 
|-------------------------------------------------------------------------- 
*/
Route::post('/create-checkout-session', [StripeController::class, 'createCheckoutSession'])->middleware('jwt.verify');
// Route::middleware(middleware: ['jwt.verify'])->group(function () {

//     // 🔐 Auth routes
//     Route::post('/logout', [AuthController::class, 'logout']);
//     Route::post('/refresh', [AuthController::class, 'refresh']);  // added as per your snippet
//     Route::post('/me', [AuthController::class, 'me']);

//     // 💳 Stripe Checkout

//     // 📦 Products
//     Route::get('/products', [ProductController::class, 'index']);
//     Route::post('/products', [ProductController::class, 'store']);
//     Route::get('/products/{id}', [ProductController::class, 'show']);
//     Route::put('/products/{id}', [ProductController::class, 'update']);
//     Route::delete('/products/{id}', [ProductController::class, 'destroy']);

//     // Alternative product routes from your second snippet
//     Route::get('/allproducts', [ProductController::class, 'allProducts']);
//     Route::post('/addproduct', [ProductController::class, 'store']);

//     // 📦 Orders
//     Route::get('/orders', [OrderController::class, 'index']);
//     Route::post('/orders', [OrderController::class, 'store']);
//     Route::delete('/orders/{id}', [OrderController::class, 'destroy']);

//     // 📊 Charts
//     Route::get('/charts/data', [Chart::class, 'getData']);

//     // Cart/Chart related routes
//     Route::post('/addchart', [Chart::class, 'addToCart']);
//     Route::post('/increment', [Chart::class, 'increment']);
//     Route::post('/decrement', [Chart::class, 'decrement']);
//     Route::put('/cart/increment/{cartId}', [Chart::class, 'increment']);
//     Route::put('/cart/decrement/{cartId}', [Chart::class, 'decrement']);
//     Route::get('/user-cart/{userId}', [Chart::class, 'getUserCart']);
// });

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Public authentication routes
// Route::post('login', [AuthController::class, 'login']);
// Route::post('register', [AuthController::class, 'register']);


Route::get('/allproducts', [ProductController::class, 'allProducts'])->middleware('jwt.verify');
Route::post('/addproduct', [ProductController::class, 'store'])->middleware('jwt.verify');
// Group routes with API 
Route::get('/orders/manager', [OrderController::class, 'getAllOrders'])->middleware('jwt.verify');
Route::post('/orders', [OrderController::class, 'store'])->middleware('jwt.verify');
Route::post('/increment', [CartController::class, 'increment'])->middleware('jwt.verify');
Route::post('/decrement', [CartController::class, 'decrement'])->middleware('jwt.verify');

Route::post('/addchart', [CartController::class, 'addToCart'])->middleware('jwt.verify');
Route::put('/cart/increment/{cartId}', [CartController::class, 'increment'])->middleware('jwt.verify');
Route::put('/cart/decrement/{cartId}', [CartController::class, 'decrement'])->middleware('jwt.verify');
Route::get('/user-cart/{userId}', [CartController::class, 'getUserCart'])->middleware('jwt.verify');
Route::post('/checkout', [ConfirmController::class, 'checkout'])->middleware('jwt.verify');
Route::post('/orders/confirm/{id}', action: [ConfirmController::class, 'confirm'])->middleware('jwt.verify');
Route::put('/orders/{id}/confirmed-by', [OrderController::class, 'updateConfirmedBy'])->middleware('jwt.verify');
Route::get('/dashboard/stats', [DashboardController::class, 'stats'])->middleware('jwt.verify');
Route::get('/dashboard/recent-orders', [DashboardController::class, 'recentOrders'])->middleware('jwt.verify');
Route::post('/comments', [CommentController::class, 'store'])->middleware('jwt.verify');
Route::get('/comments/{productId}', [CommentController::class, 'index'])->middleware('jwt.verify');
Route::delete('/comments/{id}', [CommentController::class, 'destroy'])->middleware('jwt.verify');

Route::get('/contact-us', [ContactUsController::class, 'index']);
Route::post('/contact-us', [ContactUsController::class, 'store']);
Route::get('/contact-us/{id}', [ContactUsController::class, 'show']);
Route::delete('/contact-us/{id}', [ContactUsController::class, 'destroy']);


Route::post('/genai', [GenAiController::class, 'generate'])->middleware('jwt.verify');







Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::post('/me', [AuthController::class, 'me']);


    Route::get('/orders', [OrderController::class, 'index']);
    Route::delete('/orders/{id}', [OrderController::class, 'destroy']);

});

<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductImageController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\SubCategoryController;
use App\Http\Controllers\Api\WishlistController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\NotificationController;
// Public
Route::middleware('throttle:6,1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);

    Route::post('/reset-password', [AuthController::class, 'resetPassword']);


});


Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);
Route::apiResource('sub-categories', SubCategoryController::class)->only(['index', 'show']);
Route::apiResource('brands', BrandController::class)->only(['index', 'show']);
Route::apiResource('products', ProductController::class)->only(['index', 'show']);
Route::post('/coupons/check', [CouponController::class, 'check']);

// Authenticated
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markAsRead']);
    Route::patch('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy']);

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::get('/cart', [CartController::class, 'show']);
    Route::post('/cart/items', [CartController::class, 'storeItem']);
    Route::patch('/cart/items/{cartItem}', [CartController::class, 'updateItem']);
    Route::delete('/cart/items/{cartItem}', [CartController::class, 'destroyItem']);

    Route::get('/wishlist', [WishlistController::class, 'index']);
    Route::post('/wishlist/toggle', [WishlistController::class, 'toggle']);

    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);

    Route::post('/payments', [PaymentController::class, 'store']);
    Route::get('/payments/{payment}', [PaymentController::class, 'show']);

    Route::post('/reviews', [ReviewController::class, 'store']);
    Route::patch('/reviews/{review}', [ReviewController::class, 'update']);
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy']);
});

// Admin (add an 'admin' middleware/gate once roles are wired to policies)
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::apiResource('categories', CategoryController::class)->except(['index', 'show']);
    Route::apiResource('sub-categories', SubCategoryController::class)->except(['index', 'show']);
    Route::apiResource('brands', BrandController::class)->except(['index', 'show']);
    Route::apiResource('products', ProductController::class)->except(['index', 'show']);
    Route::post('/products/{product}/images', [ProductImageController::class, 'store']);
    Route::delete('/product-images/{productImage}', [ProductImageController::class, 'destroy']);

    Route::apiResource('coupons', CouponController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus']);
});

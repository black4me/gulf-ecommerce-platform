<?php

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\VendorController;

/*
|--------------------------------------------------------------------------
| API Routes - المساراته البرمجية
|--------------------------------------------------------------------------
|
| RESTful API routes for mobile apps and frontend applications
| مسارات REST API للاطبلاقات المتحركة والتطبيقات
|
*/

Route::prefix('v1')->middleware('api')->group(function () {
    // Authentication routes - public
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/refresh', [AuthController::class, 'refresh']);

    // Protected auth routes
    Route::middleware('auth:api')->group(function () {
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::put('/auth/profile', [AuthController::class, 'updateProfile']);
    });

    // Product routes - public
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::get('/products/vendor/{vendorId}', [ProductController::class, 'byVendor']);
    Route::get('/categories', 'App\\Http\\Controllers\\Api\\CategoryController@index');
    Route::get('/categories/{id}/products', 'App\\Http\\Controllers\\Api\\CategoryController@products');

    // Search & Filter
    Route::get('/search', [ProductController::class, 'search']);
    Route::get('/products/filter/all', [ProductController::class, 'filter']);

    // Reviews - public read
    Route::get('/products/{id}/reviews', 'App\\Http\\Controllers\\Api\\ReviewController@byProduct');

    // Protected customer routes
    Route::middleware('auth:api')->group(function () {
        // Cart
        Route::get('/cart', 'App\\Http\\Controllers\\Api\\CartController@index');
        Route::post('/cart/add', 'App\\Http\\Controllers\\Api\\CartController@add');
        Route::put('/cart/{itemId}', 'App\\Http\\Controllers\\Api\\CartController@update');
        Route::delete('/cart/{itemId}', 'App\\Http\\Controllers\\Api\\CartController@remove');
        Route::post('/cart/clear', 'App\\Http\\Controllers\\Api\\CartController@clear');

        // Orders
        Route::get('/orders', [OrderController::class, 'index']);
        Route::get('/orders/{id}', [OrderController::class, 'show']);
        Route::post('/orders', [OrderController::class, 'store']);
        Route::put('/orders/{id}', [OrderController::class, 'update']);
        Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel']);

        // Wishlist
        Route::get('/wishlist', 'App\\Http\\Controllers\\Api\\WishlistController@index');
        Route::post('/wishlist/add', 'App\\Http\\Controllers\\Api\\WishlistController@add');
        Route::delete('/wishlist/{id}', 'App\\Http\\Controllers\\Api\\WishlistController@remove');

        // Reviews - protected write
        Route::post('/reviews', 'App\\Http\\Controllers\\Api\\ReviewController@store');
        Route::put('/reviews/{id}', 'App\\Http\\Controllers\\Api\\ReviewController@update');
        Route::delete('/reviews/{id}', 'App\\Http\\Controllers\\Api\\ReviewController@destroy');

        // Payment
        Route::post('/payments/process', 'App\\Http\\Controllers\\Api\\PaymentController@process');
        Route::post('/payments/verify', 'App\\Http\\Controllers\\Api\\PaymentController@verify');
        Route::get('/payments', 'App\\Http\\Controllers\\Api\\PaymentController@index');
        Route::get('/payments/{id}', 'App\\Http\\Controllers\\Api\\PaymentController@show');

        // Checkout
        Route::post('/checkout', 'App\\Http\\Controllers\\Api\\CheckoutController@process');
    });

    // Vendor Routes - protected
    Route::middleware(['auth:api', 'vendor'])->prefix('vendor')->group(function () {
        Route::get('/', [VendorController::class, 'dashboard']);
        Route::get('/analytics', [VendorController::class, 'analytics']);
        Route::get('/sales', [VendorController::class, 'sales']);

        // Products
        Route::get('/products', [VendorController::class, 'products']);
        Route::post('/products', [VendorController::class, 'storeProduct']);
        Route::put('/products/{id}', [VendorController::class, 'updateProduct']);
        Route::delete('/products/{id}', [VendorController::class, 'deleteProduct']);
        Route::put('/products/{id}/status', [VendorController::class, 'updateProductStatus']);
        Route::get('/products/{id}/stock', [VendorController::class, 'getStock']);
        Route::put('/products/{id}/stock', [VendorController::class, 'updateStock']);

        // Orders
        Route::get('/orders', [VendorController::class, 'orders']);
        Route::get('/orders/{id}', [VendorController::class, 'showOrder']);
        Route::put('/orders/{id}/status', [VendorController::class, 'updateOrderStatus']);
        Route::get('/orders/{id}/shipment', [VendorController::class, 'getShipment']);

        // Commission & Payout
        Route::get('/commissions', [VendorController::class, 'commissions']);
        Route::get('/payouts', [VendorController::class, 'payouts']);
        Route::post('/payouts/request', [VendorController::class, 'requestPayout']);

        // Profile
        Route::get('/profile', [VendorController::class, 'profile']);
        Route::put('/profile', [VendorController::class, 'updateProfile']);
    });

    // Admin Routes - protected
    Route::middleware(['auth:api', 'admin'])->prefix('admin')->group(function () {
        Route::get('/', 'App\\Http\\Controllers\\Api\\Admin\\AdminController@dashboard');

        // Users
        Route::get('/users', 'App\\Http\\Controllers\\Api\\Admin\\UserController@index');
        Route::get('/users/{id}', 'App\\Http\\Controllers\\Api\\Admin\\UserController@show');
        Route::delete('/users/{id}', 'App\\Http\\Controllers\\Api\\Admin\\UserController@destroy');

        // Vendors
        Route::get('/vendors', 'App\\Http\\Controllers\\Api\\Admin\\VendorController@index');
        Route::get('/vendors/{id}', 'App\\Http\\Controllers\\Api\\Admin\\VendorController@show');
        Route::put('/vendors/{id}/status', 'App\\Http\\Controllers\\Api\\Admin\\VendorController@updateStatus');
        Route::put('/vendors/{id}/commission', 'App\\Http\\Controllers\\Api\\Admin\\VendorController@updateCommission');

        // Products
        Route::get('/products', 'App\\Http\\Controllers\\Api\\Admin\\ProductController@index');
        Route::put('/products/{id}/status', 'App\\Http\\Controllers\\Api\\Admin\\ProductController@updateStatus');

        // Orders
        Route::get('/orders', 'App\\Http\\Controllers\\Api\\Admin\\OrderController@index');
        Route::get('/orders/{id}', 'App\\Http\\Controllers\\Api\\Admin\\OrderController@show');

        // Analytics
        Route::get('/analytics/dashboard', 'App\\Http\\Controllers\\Api\\Admin\\AnalyticsController@dashboard');
        Route::get('/analytics/sales', 'App\\Http\\Controllers\\Api\\Admin\\AnalyticsController@sales');
        Route::get('/analytics/vendors', 'App\\Http\\Controllers\\Api\\Admin\\AnalyticsController@vendors');

        // Settings
        Route::get('/settings', 'App\\Http\\Controllers\\Api\\Admin\\SettingsController@index');
        Route::put('/settings', 'App\\Http\\Controllers\\Api\\Admin\\SettingsController@update');
    });
});

// Payment gateway webhooks - public
Route::post('/webhooks/paytabs', 'App\\Http\\Controllers\\Api\\Webhooks\\PayTabsController@handle');
Route::post('/webhooks/telr', 'App\\Http\\Controllers\\Api\\Webhooks\\TelrController@handle');
Route::post('/webhooks/stripe', 'App\\Http\\Controllers\\Api\\Webhooks\\StripeController@handle');

// Health check
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'API is running',
        'timestamp' => now(),
    ]);
});

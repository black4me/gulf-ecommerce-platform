<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\VendorController;

/*
|--------------------------------------------------------------------------
| Web Routes - المسارات الويب
|--------------------------------------------------------------------------
|
| Routes for customer-facing web application (Blade templates)
| المسارات الموجهة للمتصفح - الواجهة الأمامية
|
*/

// Redirect to localized home
Route::get('/', function () {
    return redirect(app()->getLocale());
});

// Localized routes with Arabic & English support
Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localeSessionRedirect', 'localizationRedirect']
], function () {
    // Home & Static pages
    Route::get('/', function () {
        return view('home');
    })->name('home');

    Route::get('/about', function () {
        return view('pages.about');
    })->name('about');

    Route::get('/privacy-policy', function () {
        return view('pages.privacy');
    })->name('privacy');

    Route::get('/terms', function () {
        return view('pages.terms');
    })->name('terms');

    Route::get('/return-policy', function () {
        return view('pages.returns');
    })->name('returns');

    Route::get('/contact', 'App\\Http\\Controllers\\ContactController@index')
        ->name('contact');
    Route::post('/contact', 'App\\Http\\Controllers\\ContactController@store')
        ->name('contact.store');

    // Product routes
    Route::get('/products', [ProductController::class, 'index'])
        ->name('products.index');
    Route::get('/products/{slug}', [ProductController::class, 'show'])
        ->name('products.show');
    Route::get('/categories/{slug}', 'App\\Http\\Controllers\\CategoryController@show')
        ->name('categories.show');
    Route::get('/search', [ProductController::class, 'search'])
        ->name('products.search');

    // Cart routes
    Route::get('/cart', 'App\\Http\\Controllers\\CartController@index')
        ->name('cart.index');
    Route::post('/cart/add', 'App\\Http\\Controllers\\CartController@add')
        ->name('cart.add');
    Route::post('/cart/update', 'App\\Http\\Controllers\\CartController@update')
        ->name('cart.update');
    Route::post('/cart/remove', 'App\\Http\\Controllers\\CartController@remove')
        ->name('cart.remove');

    // Authenticated customer routes
    Route::middleware(['auth'])->group(function () {
        // Customer account
        Route::get('/account', 'App\\Http\\Controllers\\AccountController@index')
            ->name('account.index');
        Route::post('/account/update', 'App\\Http\\Controllers\\AccountController@update')
            ->name('account.update');
        Route::post('/account/password', 'App\\Http\\Controllers\\AccountController@updatePassword')
            ->name('account.password');

        // Orders
        Route::get('/orders', [OrderController::class, 'index'])
            ->name('orders.index');
        Route::get('/orders/{id}', [OrderController::class, 'show'])
            ->name('orders.show');

        // Checkout
        Route::get('/checkout', 'App\\Http\\Controllers\\CheckoutController@index')
            ->name('checkout.index');
        Route::post('/checkout', 'App\\Http\\Controllers\\CheckoutController@store')
            ->name('checkout.store');
        Route::post('/checkout/confirm', 'App\\Http\\Controllers\\CheckoutController@confirm')
            ->name('checkout.confirm');

        // Wishlist
        Route::get('/wishlist', 'App\\Http\\Controllers\\WishlistController@index')
            ->name('wishlist.index');
        Route::post('/wishlist/add', 'App\\Http\\Controllers\\WishlistController@add')
            ->name('wishlist.add');
        Route::post('/wishlist/remove', 'App\\Http\\Controllers\\WishlistController@remove')
            ->name('wishlist.remove');

        // Reviews
        Route::post('/reviews', 'App\\Http\\Controllers\\ReviewController@store')
            ->name('reviews.store');
    });

    // Vendor dashboard routes
    Route::prefix('vendor')->middleware(['auth', 'vendor'])->group(function () {
        Route::get('/', [VendorController::class, 'dashboard'])
            ->name('vendor.dashboard');
        Route::get('/profile', [VendorController::class, 'profile'])
            ->name('vendor.profile');
        Route::post('/profile/update', [VendorController::class, 'updateProfile'])
            ->name('vendor.profile.update');

        // Products
        Route::get('/products', [VendorController::class, 'products'])
            ->name('vendor.products');
        Route::get('/products/create', [VendorController::class, 'createProduct'])
            ->name('vendor.products.create');
        Route::post('/products', [VendorController::class, 'storeProduct'])
            ->name('vendor.products.store');
        Route::get('/products/{id}/edit', [VendorController::class, 'editProduct'])
            ->name('vendor.products.edit');
        Route::post('/products/{id}', [VendorController::class, 'updateProduct'])
            ->name('vendor.products.update');
        Route::post('/products/{id}/delete', [VendorController::class, 'deleteProduct'])
            ->name('vendor.products.delete');

        // Orders
        Route::get('/orders', [VendorController::class, 'orders'])
            ->name('vendor.orders');
        Route::get('/orders/{id}', [VendorController::class, 'showOrder'])
            ->name('vendor.orders.show');
        Route::post('/orders/{id}/status', [VendorController::class, 'updateOrderStatus'])
            ->name('vendor.orders.status');

        // Analytics & Reports
        Route::get('/analytics', [VendorController::class, 'analytics'])
            ->name('vendor.analytics');
        Route::get('/sales', [VendorController::class, 'sales'])
            ->name('vendor.sales');
        Route::get('/payouts', [VendorController::class, 'payouts'])
            ->name('vendor.payouts');
    });

    // Admin dashboard routes
    Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
        Route::get('/', 'App\\Http\\Controllers\\Admin\\AdminController@dashboard')
            ->name('admin.dashboard');

        // Users
        Route::resource('users', 'App\\Http\\Controllers\\Admin\\UserController');

        // Vendors
        Route::resource('vendors', 'App\\Http\\Controllers\\Admin\\VendorController');

        // Products
        Route::resource('products', 'App\\Http\\Controllers\\Admin\\ProductController');

        // Orders
        Route::resource('orders', 'App\\Http\\Controllers\\Admin\\OrderController');

        // Settings
        Route::get('/settings', 'App\\Http\\Controllers\\Admin\\SettingsController@index')
            ->name('admin.settings');
        Route::post('/settings', 'App\\Http\\Controllers\\Admin\\SettingsController@store')
            ->name('admin.settings.store');
    });
});

// Authentication routes
Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login')
    ->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])
    ->middleware('guest');

Route::get('/register', [AuthController::class, 'showRegister'])
    ->name('register')
    ->middleware('guest');
Route::post('/register', [AuthController::class, 'register'])
    ->middleware('guest');

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

// Payment callback routes
Route::post('/payment/callback/paytabs', 'App\\Http\\Controllers\\Payment\\PayTabsController@callback')
    ->name('payment.callback.paytabs');
Route::post('/payment/callback/telr', 'App\\Http\\Controllers\\Payment\\TelrController@callback')
    ->name('payment.callback.telr');

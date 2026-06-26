<?php

use App\Http\Controllers\Admin\CategoryMatchController;
use App\Http\Controllers\Admin\ColorMatchController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\HomeController;
// ---------------------- публичные маршруты ----------------------

// Главная
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SizeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index']);

// Каталог
Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog.index');

Route::get('/products/{slug}/{color?}', [ProductController::class, 'show'])
    ->name('products.show');

// Авторизация и регистрация
Route::get('/login', [AuthController::class, 'authorization'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'registration'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/collections/limited', [HomeController::class, 'limited'])
    ->name('collections.limited');

Route::get('/new-arrivals', [HomeController::class, 'newArrivals'])
    ->name('new-arrivals');

Route::view('/delivery', 'footer.delivery.index')
    ->name('delivery');

Route::view('/payment', 'footer.payment.index')
    ->name('payment');

Route::view('/privacy', 'footer.privacy.index')
    ->name('privacy');
// ---------------------- защищённые маршруты ----------------------

// Корзина (для авторизованных)
Route::middleware('auth')->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{slug}', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/update/{item}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{item}', [CartController::class, 'remove'])->name('cart.remove');
    Route::delete('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
    Route::get('/cart/check/{slug}', [CartController::class, 'check']);

    // Профиль
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');

    // Избранное
    Route::post('/favorites/{productColor}/toggle', [FavoriteController::class, 'toggle'])
        ->name('favorites.toggle');
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');

    Route::get('/orders/create', [OrderController::class, 'create'])
        ->name('orders.create');

    Route::post('/orders', [OrderController::class, 'store'])
        ->name('orders.store');

    Route::get('/orders/{order}', [OrderController::class, 'show'])
        ->name('orders.show');

    Route::post('/checkout/fake-pay', [OrderController::class, 'fakePay'])
        ->name('checkout.fake-pay');

    Route::get('/checkout/payment/{order}', [OrderController::class, 'paymentPage'])
        ->name('checkout.payment');

});

// Админка (только для админа)
Route::prefix('admin')
    ->middleware(['auth', 'admin'])
    ->name('admin.')
    ->group(function () {

        // Пользователи
        Route::resource('users', UserController::class)->only(['index', 'show', 'destroy']);

        // Продукты
        Route::get('/products', [ProductController::class, 'index'])->name('products.index');

        Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');

        Route::get('products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('products/{product}', [ProductController::class, 'update'])->name('products.update');

        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

        // Категории
        Route::get('categories', [CategoryController::class, 'index'])->name('categories.index'); // <-- добавлено
        Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::put('categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

        // Цвета
        Route::get('colors', [ColorController::class, 'index'])->name('colors.index');
        Route::post('colors', [ColorController::class, 'store'])->name('colors.store');
        Route::put('colors/{color}', [ColorController::class, 'toggle'])->name('colors.update');

        // Размеры в админке
        Route::get('sizes', [SizeController::class, 'index'])->name('sizes.index');
        Route::post('sizes', [SizeController::class, 'store'])->name('sizes.store');
        Route::put('sizes/{size}', [SizeController::class, 'update'])->name('sizes.update');
        Route::delete('sizes/{size}', [SizeController::class, 'destroy'])->name('sizes.destroy');

        // 📦 CHECKOUT
        Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
        Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

        Route::patch('/products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])
            ->name('products.toggle-status');

        Route::get(
            '/category-matches',
            [CategoryMatchController::class, 'index']
        )->name('category-matches.index');

        Route::post(
            '/category-matches/{category}',
            [CategoryMatchController::class, 'update']
        )->name('category-matches.update');

        Route::get(
            '/admin/color-matches',
            [ColorMatchController::class, 'index']
        )->name('admin.color-matches.index');

        Route::post(
            '/color-matches/{color}',
            [ColorMatchController::class, 'update']
        )->name('color-matches.update');

        Route::get('/orders', [OrderController::class, 'index'])
            ->name('orders.index');

Route::patch('/orders/{order}/status', [OrderController::class, 'status'])
    ->name('orders.status');

        Route::get('/orders/{order}', [OrderController::class, 'showAdmin'])
            ->name('orders.show');
    });

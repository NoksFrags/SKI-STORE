<?php

declare(strict_types=1);

use App\Controllers\AccountController;
use App\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Controllers\Admin\OrderController as AdminOrderController;
use App\Controllers\Admin\ProductController as AdminProductController;
use App\Controllers\Admin\UserController as AdminUserController;
use App\Controllers\AuthController;
use App\Controllers\CartController;
use App\Controllers\CheckoutController;
use App\Controllers\HomeController;
use App\Controllers\ShopController;
use App\Controllers\WishlistController;
use App\Core\Router;
use App\Middleware\AdminMiddleware;
use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;

$router->get('/', [HomeController::class, 'index']);
$router->get('/shop', [ShopController::class, 'index']);
$router->get('/product/{slug}', [ShopController::class, 'show']);

$router->get('/cart', [CartController::class, 'index']);
$router->post('/cart/add', [CartController::class, 'add']);
$router->post('/cart/update', [CartController::class, 'update']);
$router->post('/cart/remove', [CartController::class, 'remove']);
$router->post('/cart/clear', [CartController::class, 'clear']);

$router->group([GuestMiddleware::class], static function (Router $router): void {
    $router->get('/login', [AuthController::class, 'showLogin']);
    $router->post('/login', [AuthController::class, 'login']);
    $router->get('/register', [AuthController::class, 'showRegister']);
    $router->post('/register', [AuthController::class, 'register']);
});

$router->post('/logout', [AuthController::class, 'logout'], [AuthMiddleware::class]);

$router->group([AuthMiddleware::class], static function (Router $router): void {
    $router->get('/checkout', [CheckoutController::class, 'index']);
    $router->post('/checkout', [CheckoutController::class, 'place']);
    $router->get('/checkout/success/{id}', [CheckoutController::class, 'success']);

    $router->get('/wishlist', [WishlistController::class, 'index']);
    $router->post('/wishlist/toggle', [WishlistController::class, 'toggle']);
    $router->post('/wishlist/remove', [WishlistController::class, 'remove']);

    $router->get('/account', [AccountController::class, 'dashboard']);
    $router->get('/account/orders', [AccountController::class, 'orders']);
    $router->get('/account/orders/{id}', [AccountController::class, 'order']);
    $router->post('/account/orders/{id}/cancel', [AccountController::class, 'cancelOrder']);
    $router->get('/account/profile', [AccountController::class, 'profile']);
    $router->post('/account/profile', [AccountController::class, 'updateProfile']);
    $router->get('/account/password', [AccountController::class, 'password']);
    $router->post('/account/password', [AccountController::class, 'updatePassword']);
});

$router->group([AdminMiddleware::class], static function (Router $router): void {
    $router->get('/admin', [AdminDashboardController::class, 'index']);

    $router->get('/admin/products', [AdminProductController::class, 'index']);
    $router->get('/admin/products/create', [AdminProductController::class, 'create']);
    $router->post('/admin/products', [AdminProductController::class, 'store']);
    $router->get('/admin/products/{id}/edit', [AdminProductController::class, 'edit']);
    $router->post('/admin/products/{id}', [AdminProductController::class, 'update']);
    $router->post('/admin/products/{id}/delete', [AdminProductController::class, 'destroy']);

    $router->get('/admin/categories', [AdminCategoryController::class, 'index']);
    $router->post('/admin/categories', [AdminCategoryController::class, 'store']);
    $router->get('/admin/categories/{id}/edit', [AdminCategoryController::class, 'edit']);
    $router->post('/admin/categories/{id}', [AdminCategoryController::class, 'update']);
    $router->post('/admin/categories/{id}/delete', [AdminCategoryController::class, 'destroy']);

    $router->get('/admin/orders', [AdminOrderController::class, 'index']);
    $router->get('/admin/orders/{id}', [AdminOrderController::class, 'show']);
    $router->post('/admin/orders/{id}/status', [AdminOrderController::class, 'updateStatus']);

    $router->get('/admin/users', [AdminUserController::class, 'index']);
    $router->post('/admin/users/{id}/role', [AdminUserController::class, 'updateRole']);
    $router->post('/admin/users/{id}/active', [AdminUserController::class, 'toggleActive']);
});

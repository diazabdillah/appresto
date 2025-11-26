<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\MidtransController;
use App\Http\Controllers\Admin\TableController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;

/*
|--------------------------------------------------------------------------
| PUBLIK & QR CODE ROUTES (Akses Menu)
|--------------------------------------------------------------------------
*/
Route::get('/', [CustomerController::class, 'index'])->name('customer.index');


// Akses Menu melalui QR Code (Route Model Binding: {table})
Route::get('/menu/{table}', [CustomerController::class, 'showMenu'])->name('customer.menu');

// Keranjang (Cart)
Route::get('/keranjang', [CustomerController::class, 'showCart'])->name('customer.cart.show');
Route::post('/add-to-cart', [CustomerController::class, 'addToCart'])->name('customer.cart.add');
Route::put('/update-cart', [CustomerController::class, 'updateCart'])->name('customer.cart.update');
Route::get('/remove-cart/{productId}', [CustomerController::class, 'removeCartItem'])->name('customer.cart.remove');
Route::put('/update-cart', [CustomerController::class, 'updateCart'])->name('customer.cart.update');
Route::get('/customer/category/{category}', [CustomerController::class, 'showCategoryDetail'])
    ->name('customer.category.show');
Route::post('/ajax/cart/update-qty/{id}', [CustomerController::class, 'updateCartQtyAjax'])->name('customer.cart.update.ajax');
Route::delete('/cart/remove-item/{id}', 'App\Http\Controllers\CustomerController@removeCartItem');
Route::get('/product/{product}', [CustomerController::class, 'showProductDetail'])
    ->name('customer.product.show');
// Checkout & Pembayaran Dine-in (Tanpa Login)
// Route ini memicu Order Creation, khusus untuk Dine-in
Route::post('/checkout-dinein', [CustomerController::class, 'createOrderDineIn'])->name('customer.order.create.dinein');
// routes/web.php
// routes/web.php
Route::get('/scan', function () {
    return view('customer.scan_page');
})->name('customer.scan_qr');
// Route Detail Pesanan (Menggunakan Route Model Binding)
Route::get('/order/{order}', [CustomerController::class, 'showOrderDetail'])->name('customer.order.detail');
// Histori Pembayaran Meja (Dine-in)
Route::get('/table-history', [CustomerController::class, 'showTableHistory'])->name('customer.history.show');

// Midtrans Webhook Notification
Route::post('/midtrans-notification', [MidtransController::class, 'notificationHandler']);

// Halaman Status Order (Opsional, untuk tracking status setelah Midtrans)
Route::get('/order-status/{order}', [CustomerController::class, 'showOrderStatus'])->name('customer.order.status');
// routes/web.php (di dalam grup terotentikasi atau sebagai rute publik)

// Route untuk membatalkan order (hanya jika pending)
Route::delete('/order/{order}/cancel', [CustomerController::class, 'cancelOrder'])->name('customer.order.cancel');
Route::get('/menu-delivery', [CustomerController::class, 'showDeliveryMenu'])->name('customer.menu.delivery');
/*
|--------------------------------------------------------------------------
| GUEST ROUTES (LOGIN & REGISTER - Menggunakan AuthController Buatan Sendiri)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    // Login
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // Register
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});


/*
|--------------------------------------------------------------------------
| AUTHENTICATED ROUTES (Pelanggan & Staff/Admin)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Checkout Delivery/Takeaway (Pelanggan Login)
    Route::post('/checkout-auth', [CustomerController::class, 'createOrderAuthenticated'])->name('app.order.create.auth');

    // Riwayat Order Pribadi (Pelanggan Login)
    Route::get('/my-orders', [CustomerController::class, 'showMyOrders'])->name('app.orders.history');
});


/*
|--------------------------------------------------------------------------
| ADMIN/STAFF ROUTES (Dilindungi oleh role check di Controller atau Middleware)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'can:access-admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard Admin
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // CRUD Meja
    Route::resource('tables', TableController::class)->except(['show']);
    
    // CRUD Produk & Kategori
    Route::resource('products', ProductController::class);
    Route::resource('categories', CategoryController::class)->except(['show']);

    // Manajemen Order (Opsional: Terima, Proses, Selesai)
    // Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
});
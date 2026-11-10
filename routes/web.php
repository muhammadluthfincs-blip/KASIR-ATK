<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CashierReportController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\StockOutController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Root redirect
Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->role === 'admin'
            ? redirect()->route('dashboard')
            : redirect()->route('pos.index');
    }
    return redirect()->route('login');
});

// Authentication
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // POS & Sales (Accessible by both Admin and Kasir)
    Route::get('/pos', [SaleController::class, 'pos'])->name('pos.index');
    Route::post('/pos/checkout', [SaleController::class, 'store'])->name('sales.store');
    Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
    Route::get('/sales/{sale}', [SaleController::class, 'show'])->name('sales.show');
    Route::get('/sales/{sale}/receipt', [SaleController::class, 'receipt'])->name('sales.receipt');
    Route::get('/products/search-json', [ProductController::class, 'searchJson'])->name('products.search-json');

    // Laporan Harian Kasir (Shift Closing)
    Route::get('/cashier/closing', [CashierReportController::class, 'closing'])->name('cashier.closing');
    Route::get('/cashier/closing/print-thermal', [CashierReportController::class, 'printThermal'])->name('cashier.closing.print-thermal');
    Route::get('/cashier/closing/print-a4', [CashierReportController::class, 'printA4'])->name('cashier.closing.print-a4');

    // Pusat Notifikasi Sistem
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');

    // Katalog Produk & Cek Stok (Accessible by both Admin and Kasir)
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
    Route::get('/stocks', [StockController::class, 'index'])->name('stocks.index');
    Route::get('/stocks/{product}/card', [StockController::class, 'card'])->name('stocks.card');

    // Admin Only Routes
    Route::middleware('role:admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Master Data CRUD (Admin only)
        Route::resource('categories', CategoryController::class)->except(['create', 'show', 'edit']);
        Route::resource('suppliers', SupplierController::class)->except(['create', 'show', 'edit']);
        Route::get('/products-create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::patch('/products/{product}', [ProductController::class, 'update']);
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

        // Purchases / Barang Masuk
        Route::get('/purchases', [PurchaseController::class, 'index'])->name('purchases.index');
        Route::get('/purchases/create', [PurchaseController::class, 'create'])->name('purchases.create');
        Route::post('/purchases', [PurchaseController::class, 'store'])->name('purchases.store');
        Route::get('/purchases/{purchase}', [PurchaseController::class, 'show'])->name('purchases.show');
        Route::get('/purchases/{purchase}/print', [PurchaseController::class, 'print'])->name('purchases.print');

        // Barang Keluar (Non-penjualan)
        Route::get('/stock-outs', [StockOutController::class, 'index'])->name('stock-outs.index');
        Route::get('/stock-outs/create', [StockOutController::class, 'create'])->name('stock-outs.create');
        Route::post('/stock-outs', [StockOutController::class, 'store'])->name('stock-outs.store');
        Route::get('/stock-outs/{stockOut}', [StockOutController::class, 'show'])->name('stock-outs.show');

        // Reports & Exports
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/print', [ReportController::class, 'print'])->name('reports.print');
        Route::get('/reports/export-excel', [ReportController::class, 'exportExcel'])->name('reports.export-excel');

        // User Management
        Route::resource('users', UserController::class)->except(['create', 'show', 'edit']);
    });
});

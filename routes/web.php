<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\SuperAdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReceiptController;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

// Route::get('/', function () {
//     return redirect()->route('dashboard');
// });

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::middleware('auth')->group(function () {
    // Logout is allowed for all authenticated users
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Receipts are allowed for cashier and superadmin (since they see POS and transactions)
    Route::middleware('role:kasir,superadmin')->group(function () {
        Route::get('/receipt/{transaction}/download', [ReceiptController::class, 'download'])->name('receipt.download');
        Route::get('/receipt/{transaction}/preview',  [ReceiptController::class, 'preview'])->name('receipt.preview');
    });

    // --- KASIR & SUPERADMIN MODULES ---
    Route::middleware('role:kasir,superadmin')->group(function () {
        Route::get('/kasir/dashboard', [DashboardController::class, 'kasirDashboard'])->name('kasir.dashboard');
        Route::get('/cashier', [DashboardController::class, 'cashier'])->name('cashier');

        Route::prefix('products')->name('products.')->group(function () {
            Route::get('/', [DashboardController::class, 'products'])->name('index');
            Route::post('/', [App\Http\Controllers\ProductController::class, 'store'])->name('store');
            Route::put('/{product}', [App\Http\Controllers\ProductController::class, 'update'])->name('update');
            Route::delete('/{product}', [App\Http\Controllers\ProductController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('transactions')->name('transactions.')->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'transactions'])->name('index');
            Route::post('/', [App\Http\Controllers\TransactionController::class, 'store'])->name('store');
        });
    });

    // --- OWNER & SUPERADMIN MODULES ---
    Route::middleware('role:owner,superadmin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [SuperAdminController::class, 'index'])->name('index');
        });
    });

    // --- SUPERADMIN ONLY MODULES ---
    Route::middleware('role:superadmin')->group(function () {
        Route::get('/users', [SuperAdminController::class, 'users'])->name('users.index');
        Route::delete('/transactions/{transaction}', [App\Http\Controllers\TransactionController::class, 'destroy'])->name('transactions.destroy');
    });
});

// --- Midtrans Webhook (no auth, no CSRF — verified by Midtrans signature key) ---
Route::post('/midtrans/notification', [App\Http\Controllers\MidtransWebhookController::class, 'handle'])
    ->name('midtrans.webhook');

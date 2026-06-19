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
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Route::get('/dashboard', function () {
    //     return view('dashboard');
    // })->name('dashboard');

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
        Route::delete('/{transaction}', [App\Http\Controllers\TransactionController::class, 'destroy'])->name('destroy');
    });

    Route::get('/receipt/{transaction}/download', [ReceiptController::class, 'download'])->name('receipt.download');
    Route::get('/receipt/{transaction}/preview',  [ReceiptController::class, 'preview'])->name('receipt.preview');

    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/{shop}', [SuperAdminController::class, 'index'])->name('index');
        Route::patch('/update/{shop}', [SuperAdminController::class, 'update'])->name('update');
        Route::patch('/upload-logo/{shop}', [SuperAdminController::class, 'uploadLogo'])->name('logo');
        Route::patch('/update-theme/{shop}', [SuperAdminController::class, 'updateTheme'])->name('updateTheme');
    });

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

});

// --- Midtrans Webhook (no auth, no CSRF — verified by Midtrans signature key) ---
Route::post('/midtrans/notification', [App\Http\Controllers\MidtransWebhookController::class, 'handle'])
    ->name('midtrans.webhook');

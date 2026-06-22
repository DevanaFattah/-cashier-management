<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;

Route::middleware(['web', 'auth'])->group(function () {
    // Dashboard Stats
    Route::middleware('role:owner,superadmin')->group(function () {
        Route::get('/dashboard/stats', [ApiController::class, 'getDashboardStats']);
    });

    Route::middleware('role:kasir,superadmin')->group(function () {
        Route::get('/kasir/stats', [ApiController::class, 'getKasirStats']);
    });

    // Products
    Route::get('/products', [ApiController::class, 'getProducts']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{product}', [ProductController::class, 'update']);
    Route::delete('/products/{product}', [ProductController::class, 'destroy']);

    // Transactions
    Route::get('/transactions', [ApiController::class, 'getTransactions']);
    Route::post('/transactions', [TransactionController::class, 'store']);
    Route::delete('/transactions/{transaction}', [TransactionController::class, 'destroy']);

    // Settings
    Route::get('/settings', [ApiController::class, 'getSettings']);
    Route::put('/settings', [ApiController::class, 'updateSettings']);
    Route::post('/settings/logo', [ApiController::class, 'uploadLogo']);
    Route::put('/settings/theme', [ApiController::class, 'updateTheme']);
});

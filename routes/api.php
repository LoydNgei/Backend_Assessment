<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\TransactionController;

Route::prefix('v1')->group(function () {
    // Users
    Route::post('/users', [UserController::class, 'store']);
    Route::get('/users/{user}', [UserController::class, 'show']);

    // Wallets
    Route::post('/wallets', [WalletController::class, 'store']);
    Route::get('/wallets/{wallet}', [WalletController::class, 'show']);

    // Transactions
    Route::post('/transactions', [TransactionController::class, 'store']);
});

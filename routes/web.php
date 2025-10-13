<?php

use Illuminate\Support\Facades\Route;
use SourngSeng\AbaPayway\Http\Controllers\AbaPaywayController;

Route::group(['prefix' => 'aba-payway', 'as' => 'aba-payway.'], function () {
    // Payment form display
    Route::get('/payment-form', [AbaPaywayController::class, 'showPaymentForm'])
        ->name('payment-form');
    
    // Create payment (POST from your application)
    Route::post('/create-payment', [AbaPaywayController::class, 'createPayment'])
        ->name('create-payment');
    
    // Return URL (where ABA PayWay redirects after payment)
    Route::match(['GET', 'POST'], '/return', [AbaPaywayController::class, 'handleReturn'])
        ->name('return');
    
    // Webhook/Callback URL (for server-to-server notifications)
    Route::post('/webhook', [AbaPaywayController::class, 'handleWebhook'])
        ->name('webhook');
    
    // API endpoint to check transaction status
    Route::get('/transaction/{transactionId}/status', [AbaPaywayController::class, 'getTransactionStatus'])
        ->name('transaction.status');
});
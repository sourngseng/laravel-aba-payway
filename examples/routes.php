<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

/*
|--------------------------------------------------------------------------
| Example Payment Routes
|--------------------------------------------------------------------------
|
| Add these routes to your Laravel application's routes/web.php file
| to integrate with the ABA PayWay package.
|
*/

// Payment routes
Route::group(['prefix' => 'payment', 'as' => 'payment.'], function () {
    // Show payment form
    Route::get('/', [PaymentController::class, 'showPaymentForm'])->name('form');
    
    // Process payment
    Route::post('/process', [PaymentController::class, 'processPayment'])->name('process');
    
    // Payment result pages
    Route::get('/success', [PaymentController::class, 'paymentSuccess'])->name('success');
    Route::get('/cancelled', [PaymentController::class, 'paymentCancelled'])->name('cancelled');
    Route::get('/pending', [PaymentController::class, 'paymentPending'])->name('pending');
    Route::get('/failed', [PaymentController::class, 'paymentFailed'])->name('failed');
});

// Admin routes (add authentication middleware as needed)
Route::group(['prefix' => 'admin/payments', 'as' => 'admin.payments.', 'middleware' => ['auth', 'admin']], function () {
    // List all transactions
    Route::get('/', [PaymentController::class, 'adminTransactions'])->name('index');
    
    // View transaction details
    Route::get('/{transactionId}', [PaymentController::class, 'adminTransactionDetails'])->name('show');
});

// API routes for checking transaction status
Route::group(['prefix' => 'api/payment', 'as' => 'api.payment.'], function () {
    // Get transaction status
    Route::get('/status/{transactionId}', [PaymentController::class, 'apiTransactionStatus'])->name('status');
});
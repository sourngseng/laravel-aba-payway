<?php

return [
    /*
    |--------------------------------------------------------------------------
    | ABA PayWay Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration options for ABA PayWay payment gateway integration
    |
    */

    // Merchant credentials
    'merchant_id' => env('ABA_PAYWAY_MERCHANT_ID'),
    'merchant_secret' => env('ABA_PAYWAY_MERCHANT_SECRET'),

    // API URLs
    'api_url' => env('ABA_PAYWAY_API_URL', 'https://checkout-sandbox.payway.com.kh/api/payment-gateway/v1/payments/purchase'),
    'checkout_url' => env('ABA_PAYWAY_CHECKOUT_URL', 'https://checkout-sandbox.payway.com.kh'),

    // Environment
    'sandbox' => env('ABA_PAYWAY_SANDBOX', true),

    // URLs
    'return_url' => env('ABA_PAYWAY_RETURN_URL', '/aba-payway/return'),
    'continue_success_url' => env('ABA_PAYWAY_CONTINUE_SUCCESS_URL', '/payment/success'),
    'cancel_url' => env('ABA_PAYWAY_CANCEL_URL', '/payment/cancel'),

    // Currency
    'currency' => env('ABA_PAYWAY_CURRENCY', 'USD'),

    // Transaction settings
    'payment_option' => env('ABA_PAYWAY_PAYMENT_OPTION', 'abapay'), // abapay, cards, etc.
    
    // Hash algorithm
    'hash_algorithm' => env('ABA_PAYWAY_HASH_ALGORITHM', 'sha512'),

    // Webhook/Callback settings
    'webhook_secret' => env('ABA_PAYWAY_WEBHOOK_SECRET'),
    'verify_ssl' => env('ABA_PAYWAY_VERIFY_SSL', true),

    // Transaction table name
    'transaction_table' => env('ABA_PAYWAY_TRANSACTION_TABLE', 'aba_payway_transactions'),
];
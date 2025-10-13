# Laravel ABA PayWay

A comprehensive Laravel package for integrating with ABA PayWay, Cambodia's leading payment gateway. This package provides a simple and secure way to process payments through ABA PayWay's API.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/sourngseng/laravel-aba-payway.svg?style=flat-square)](https://packagist.org/packages/sourngseng/laravel-aba-payway)
[![Total Downloads](https://img.shields.io/packagist/dt/sourngseng/laravel-aba-payway.svg?style=flat-square)](https://packagist.org/packages/sourngseng/laravel-aba-payway)
[![License](https://img.shields.io/packagist/l/sourngseng/laravel-aba-payway.svg?style=flat-square)](https://packagist.org/packages/sourngseng/laravel-aba-payway)

## Features

- 🔐 Secure hash-based authentication
- 💳 Support for multiple payment options (ABA Pay, Cards)
- 🔄 Automatic transaction management
- 📝 Comprehensive logging
- 🧪 Callback/webhook handling
- 📊 Transaction status tracking
- 🎨 Customizable payment forms
- ✅ Comprehensive test coverage
- 🌐 Multi-currency support (USD, KHR)

## Requirements

- PHP 8.1 or higher
- Laravel 10.x, 11.x, or 12.x
- GuzzleHTTP 7.x

## Installation

Install the package via Composer:

```bash
composer require sourngseng/laravel-aba-payway
```

### Publish Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag=aba-payway-config
```

### Publish Migrations

Publish and run the migrations:

```bash
php artisan vendor:publish --tag=aba-payway-migrations
php artisan migrate
```

### Publish Views (Optional)

If you want to customize the payment form:

```bash
php artisan vendor:publish --tag=aba-payway-views
```

## Configuration

Add the following environment variables to your `.env` file:

```env
# ABA PayWay Configuration
ABA_PAYWAY_MERCHANT_ID=your_merchant_id
ABA_PAYWAY_MERCHANT_SECRET=your_merchant_secret
ABA_PAYWAY_SANDBOX=true

# URLs (for production, update these to production URLs)
ABA_PAYWAY_API_URL=https://checkout-sandbox.payway.com.kh/api/payment-gateway/v1/payments/purchase
ABA_PAYWAY_CHECKOUT_URL=https://checkout-sandbox.payway.com.kh

# Return URLs
ABA_PAYWAY_RETURN_URL=/aba-payway/return
ABA_PAYWAY_CONTINUE_SUCCESS_URL=/payment/success
ABA_PAYWAY_CANCEL_URL=/payment/cancel

# Payment Settings
ABA_PAYWAY_CURRENCY=USD
ABA_PAYWAY_PAYMENT_OPTION=abapay
ABA_PAYWAY_HASH_ALGORITHM=sha512

# Optional Webhook Secret
ABA_PAYWAY_WEBHOOK_SECRET=your_webhook_secret
```

## Usage

### Basic Payment Creation

```php
use SourngSeng\AbaPayway\Facades\AbaPayway;

// Create a payment
$paymentData = [
    'amount' => 100.00,
    'currency' => 'USD',
    'email' => 'customer@example.com',
    'phone' => '+855123456789',
    'firstname' => 'John',
    'lastname' => 'Doe',
    'items' => 'Premium Subscription',
    'metadata' => [
        'order_id' => 'ORD_123',
        'user_id' => 456
    ]
];

$payment = AbaPayway::createPayment($paymentData);
```

### Using the Controller

You can create payments using the built-in controller by posting to the route:

```php
// In your form or controller
Route::post('/create-payment', function () {
    return redirect()->route('aba-payway.create-payment')->with([
        'amount' => 50.00,
        'currency' => 'USD',
        'email' => 'customer@example.com',
        'items' => 'Product Purchase'
    ]);
});
```

### Manual Payment Form

If you prefer to create your own payment form:

```html
<form action="{{ config('aba-payway.checkout_url') }}/purchase" method="POST">
    @foreach($paymentData as $key => $value)
        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
    @endforeach
    <button type="submit">Pay with ABA PayWay</button>
</form>
```

### Handling Callbacks

The package automatically handles callbacks from ABA PayWay. You can listen for payment events:

```php
use SourngSeng\AbaPayway\Models\AbaPaywayTransaction;

// Check transaction status
$transaction = AbaPayway::getTransactionStatus('TXN_123456');

if ($transaction->isSuccessful()) {
    // Payment completed successfully
    // Update your order status, send emails, etc.
} elseif ($transaction->isPending()) {
    // Payment is still being processed
} else {
    // Payment failed
}
```

### Transaction Model

You can work with transactions using the Eloquent model:

```php
use SourngSeng\AbaPayway\Models\AbaPaywayTransaction;

// Get all successful transactions
$successfulTransactions = AbaPaywayTransaction::successful()->get();

// Get pending transactions
$pendingTransactions = AbaPaywayTransaction::pending()->get();

// Get failed transactions
$failedTransactions = AbaPaywayTransaction::failed()->get();

// Get transactions for a specific customer
$customerTransactions = AbaPaywayTransaction::where('customer_email', 'customer@example.com')->get();
```

## Routes

The package registers the following routes:

- `GET|POST /aba-payway/return` - Payment return handler
- `POST /aba-payway/webhook` - Webhook/callback handler
- `GET /aba-payway/payment-form` - Payment form display
- `POST /aba-payway/create-payment` - Create payment endpoint
- `GET /aba-payway/transaction/{id}/status` - Transaction status API

## API Reference

### AbaPayway Facade Methods

#### `createPayment(array $paymentData): array`

Creates a new payment request.

**Parameters:**
- `amount` (float, required): Payment amount
- `currency` (string, optional): Currency code (USD, KHR)
- `email` (string, optional): Customer email
- `phone` (string, optional): Customer phone
- `firstname` (string, optional): Customer first name
- `lastname` (string, optional): Customer last name
- `items` (string, optional): Description of items
- `metadata` (array, optional): Additional data

#### `verifyCallback(array $callbackData): bool`

Verifies the authenticity of a callback from ABA PayWay.

#### `processCallback(array $callbackData): bool`

Processes a callback and updates the transaction status.

#### `getTransactionStatus(string $transactionId): ?AbaPaywayTransaction`

Retrieves the current status of a transaction.

### Transaction Model Methods

#### Status Checking Methods

```php
$transaction->isSuccessful(); // Returns true if payment succeeded
$transaction->isPending();    // Returns true if payment is pending
$transaction->hasFailed();    // Returns true if payment failed
```

#### Scopes

```php
AbaPaywayTransaction::successful()->get(); // Get successful transactions
AbaPaywayTransaction::pending()->get();    // Get pending transactions
AbaPaywayTransaction::failed()->get();     // Get failed transactions
```

## Production Configuration

For production use, update your environment variables:

```env
ABA_PAYWAY_SANDBOX=false
ABA_PAYWAY_API_URL=https://checkout.payway.com.kh/api/payment-gateway/v1/payments/purchase
ABA_PAYWAY_CHECKOUT_URL=https://checkout.payway.com.kh
ABA_PAYWAY_VERIFY_SSL=true
```

## Testing

Run the package tests:

```bash
composer test
```

Or run PHPUnit directly:

```bash
vendor/bin/phpunit
```

## Security

### Hash Verification

All payments are secured using SHA-512 hash verification. The package automatically generates and verifies hashes to ensure data integrity.

### SSL/TLS

Always use SSL/TLS in production. The package supports SSL verification for webhook calls.

### Environment Variables

Never commit sensitive information like merchant secrets to version control. Always use environment variables.

## Error Handling

The package includes comprehensive error handling:

```php
use SourngSeng\AbaPayway\Exceptions\AbaPaywayException;

try {
    $payment = AbaPayway::createPayment($paymentData);
} catch (AbaPaywayException $e) {
    // Handle ABA PayWay specific errors
    Log::error('Payment creation failed: ' . $e->getMessage());
} catch (\Exception $e) {
    // Handle general errors
    Log::error('Unexpected error: ' . $e->getMessage());
}
```

## Logging

The package logs important events automatically:

- Payment creation
- Callback processing
- Transaction status updates
- Errors and exceptions

Logs are written to Laravel's default log channel.

## Events

You can listen for transaction updates by monitoring the database or creating custom event listeners:

```php
// In your EventServiceProvider
use SourngSeng\AbaPayway\Models\AbaPaywayTransaction;

// Listen for model events
AbaPaywayTransaction::updated(function ($transaction) {
    if ($transaction->isSuccessful()) {
        // Handle successful payment
        event(new PaymentSuccessful($transaction));
    }
});
```

## Troubleshooting

### Common Issues

1. **Invalid Hash Error**
   - Ensure your merchant secret is correct
   - Check that all required fields are included in hash generation
   - Verify the hash algorithm matches ABA PayWay's requirements

2. **Transaction Not Found**
   - Check that the transaction ID is correct
   - Ensure the database migration has been run
   - Verify the transaction was created successfully

3. **Callback Not Working**
   - Ensure your webhook URL is accessible from the internet
   - Check that the route is not protected by CSRF middleware
   - Verify your webhook secret (if using one)

### Debug Mode

Enable debug logging in your `.env`:

```env
LOG_LEVEL=debug
```

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).

## Support

If you encounter any issues or have questions:

1. Check the [Issues](https://github.com/sourngseng/laravel-aba-payway/issues) page
2. Create a new issue if your problem isn't already reported
3. Provide detailed information about your setup and the issue

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [Sourng Seng](https://github.com/sourngseng)
- [All Contributors](https://github.com/sourngseng/laravel-aba-payway/contributors)

## Disclaimer

This package is not officially affiliated with ABA Bank or ABA PayWay. Use at your own risk and ensure compliance with ABA PayWay's terms of service.

<?php

namespace SourngSeng\AbaPayway\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use SourngSeng\AbaPayway\Models\AbaPaywayTransaction;
use SourngSeng\AbaPayway\Exceptions\AbaPaywayException;

class AbaPaywayService
{
    protected string $merchantId;
    protected string $merchantSecret;
    protected string $apiUrl;
    protected bool $sandbox;

    public function __construct(string $merchantId, string $merchantSecret, string $apiUrl, bool $sandbox = true)
    {
        $this->merchantId = $merchantId;
        $this->merchantSecret = $merchantSecret;
        $this->apiUrl = $apiUrl;
        $this->sandbox = $sandbox;
    }

    /**
     * Create a payment request
     */
    public function createPayment(array $paymentData): array
    {
        $transactionId = $this->generateTransactionId();
        
        $payment = [
            'req_time' => now()->format('YmdHis'),
            'merchant_id' => $this->merchantId,
            'tran_id' => $transactionId,
            'amount' => $paymentData['amount'],
            'currency' => $paymentData['currency'] ?? config('aba-payway.currency', 'USD'),
            'payment_option' => $paymentData['payment_option'] ?? config('aba-payway.payment_option', 'abapay'),
            'return_url' => $paymentData['return_url'] ?? url(config('aba-payway.return_url')),
            'continue_success_url' => $paymentData['continue_success_url'] ?? url(config('aba-payway.continue_success_url')),
            'cancel_url' => $paymentData['cancel_url'] ?? url(config('aba-payway.cancel_url')),
            'firstname' => $paymentData['firstname'] ?? '',
            'lastname' => $paymentData['lastname'] ?? '',
            'phone' => $paymentData['phone'] ?? '',
            'email' => $paymentData['email'] ?? '',
            'items' => $paymentData['items'] ?? 'Payment',
            'shipping' => $paymentData['shipping'] ?? 0,
        ];

        // Generate hash
        $payment['hash'] = $this->generateHash($payment);

        // Store transaction
        $this->storeTransaction($payment, $paymentData);

        return $payment;
    }

    /**
     * Generate payment hash
     */
    public function generateHash(array $data): string
    {
        $hashString = $data['req_time'] . 
                     $data['merchant_id'] . 
                     $data['tran_id'] . 
                     $data['amount'] . 
                     $data['currency'] . 
                     $data['payment_option'] . 
                     $data['return_url'] . 
                     $data['continue_success_url'] . 
                     $data['cancel_url'] . 
                     $data['firstname'] . 
                     $data['lastname'] . 
                     $data['phone'] . 
                     $data['email'] . 
                     $data['items'] . 
                     $data['shipping'] . 
                     $this->merchantSecret;

        return hash(config('aba-payway.hash_algorithm', 'sha512'), $hashString);
    }

    /**
     * Verify callback hash
     */
    public function verifyCallback(array $callbackData): bool
    {
        if (!isset($callbackData['hash'])) {
            return false;
        }

        $hash = $callbackData['hash'];
        unset($callbackData['hash']);

        $expectedHash = $this->generateCallbackHash($callbackData);
        
        return hash_equals($expectedHash, $hash);
    }

    /**
     * Generate callback hash
     */
    protected function generateCallbackHash(array $data): string
    {
        $hashString = ($data['tran_id'] ?? '') .
                     ($data['status'] ?? '') .
                     ($data['amount'] ?? '') .
                     ($data['currency'] ?? '') .
                     ($data['req_time'] ?? '') .
                     $this->merchantSecret;

        return hash(config('aba-payway.hash_algorithm', 'sha512'), $hashString);
    }

    /**
     * Process payment callback
     */
    public function processCallback(array $callbackData): bool
    {
        try {
            if (!$this->verifyCallback($callbackData)) {
                Log::error('ABA PayWay: Invalid callback hash', $callbackData);
                return false;
            }

            $transaction = AbaPaywayTransaction::where('transaction_id', $callbackData['tran_id'])->first();
            
            if (!$transaction) {
                Log::error('ABA PayWay: Transaction not found', ['tran_id' => $callbackData['tran_id']]);
                return false;
            }

            $transaction->update([
                'status' => $this->mapStatus($callbackData['status']),
                'payment_response' => $callbackData,
                'processed_at' => now(),
            ]);

            Log::info('ABA PayWay: Transaction updated', [
                'tran_id' => $callbackData['tran_id'],
                'status' => $callbackData['status']
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('ABA PayWay: Callback processing error', [
                'error' => $e->getMessage(),
                'data' => $callbackData
            ]);
            return false;
        }
    }

    /**
     * Get transaction status
     */
    public function getTransactionStatus(string $transactionId): ?AbaPaywayTransaction
    {
        return AbaPaywayTransaction::where('transaction_id', $transactionId)->first();
    }

    /**
     * Generate unique transaction ID
     */
    protected function generateTransactionId(): string
    {
        return 'TXN_' . time() . '_' . random_int(1000, 9999);
    }

    /**
     * Store transaction in database
     */
    protected function storeTransaction(array $paymentData, array $originalData): void
    {
        AbaPaywayTransaction::create([
            'transaction_id' => $paymentData['tran_id'],
            'merchant_id' => $paymentData['merchant_id'],
            'amount' => $paymentData['amount'],
            'currency' => $paymentData['currency'],
            'payment_option' => $paymentData['payment_option'],
            'customer_email' => $paymentData['email'],
            'customer_phone' => $paymentData['phone'],
            'customer_name' => trim($paymentData['firstname'] . ' ' . $paymentData['lastname']),
            'items' => $paymentData['items'],
            'status' => 'pending',
            'payment_request' => $paymentData,
            'metadata' => $originalData['metadata'] ?? null,
            'created_at' => now(),
        ]);
    }

    /**
     * Map ABA PayWay status to internal status
     */
    protected function mapStatus(string $abaStatus): string
    {
        return match($abaStatus) {
            '0' => 'success',
            '1' => 'failed',
            '2' => 'pending',
            default => 'unknown'
        };
    }

    /**
     * Get checkout URL for payment
     */
    public function getCheckoutUrl(): string
    {
        return config('aba-payway.checkout_url') . '/purchase';
    }

    /**
     * Validate payment data
     */
    public function validatePaymentData(array $data): void
    {
        $required = ['amount'];
        
        foreach ($required as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                throw new AbaPaywayException("Required field '{$field}' is missing or empty");
            }
        }

        if (!is_numeric($data['amount']) || $data['amount'] <= 0) {
            throw new AbaPaywayException("Amount must be a positive number");
        }
    }
}
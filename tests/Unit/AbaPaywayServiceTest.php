<?php

namespace SourngSeng\AbaPayway\Tests\Unit;

use SourngSeng\AbaPayway\Services\AbaPaywayService;
use SourngSeng\AbaPayway\Tests\TestCase;

class AbaPaywayServiceTest extends TestCase
{
    protected AbaPaywayService $service;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->service = new AbaPaywayService(
            'TEST_MERCHANT_ID',
            'TEST_MERCHANT_SECRET',
            'https://checkout-sandbox.payway.com.kh/api/payment-gateway/v1/payments/purchase',
            true
        );
    }

    public function test_create_payment_generates_valid_data()
    {
        $paymentData = [
            'amount' => 100.00,
            'currency' => 'USD',
            'email' => 'test@example.com',
            'firstname' => 'John',
            'lastname' => 'Doe',
            'phone' => '+855123456789',
            'items' => 'Test Product',
        ];

        $result = $this->service->createPayment($paymentData);

        $this->assertIsArray($result);
        $this->assertEquals('TEST_MERCHANT_ID', $result['merchant_id']);
        $this->assertEquals(100.00, $result['amount']);
        $this->assertEquals('USD', $result['currency']);
        $this->assertEquals('test@example.com', $result['email']);
        $this->assertArrayHasKey('hash', $result);
        $this->assertArrayHasKey('tran_id', $result);
        $this->assertArrayHasKey('req_time', $result);
    }

    public function test_generate_hash_creates_consistent_hash()
    {
        $data = [
            'req_time' => '20240101120000',
            'merchant_id' => 'TEST_MERCHANT_ID',
            'tran_id' => 'TEST_TRANSACTION_ID',
            'amount' => 100.00,
            'currency' => 'USD',
            'payment_option' => 'abapay',
            'return_url' => 'https://example.com/return',
            'continue_success_url' => 'https://example.com/success',
            'cancel_url' => 'https://example.com/cancel',
            'firstname' => 'John',
            'lastname' => 'Doe',
            'phone' => '+855123456789',
            'email' => 'test@example.com',
            'items' => 'Test Product',
            'shipping' => 0,
        ];

        $hash1 = $this->service->generateHash($data);
        $hash2 = $this->service->generateHash($data);

        $this->assertEquals($hash1, $hash2);
        $this->assertIsString($hash1);
        $this->assertEquals(128, strlen($hash1)); // SHA512 produces 128 character hex string
    }

    public function test_verify_callback_with_valid_data()
    {
        $callbackData = [
            'tran_id' => 'TEST_TRANSACTION_ID',
            'status' => '0',
            'amount' => '100.00',
            'currency' => 'USD',
            'req_time' => '20240101120000',
        ];

        // Generate hash for test data
        $hashString = $callbackData['tran_id'] . 
                     $callbackData['status'] . 
                     $callbackData['amount'] . 
                     $callbackData['currency'] . 
                     $callbackData['req_time'] . 
                     'TEST_MERCHANT_SECRET';
        
        $callbackData['hash'] = hash('sha512', $hashString);

        $result = $this->service->verifyCallback($callbackData);
        $this->assertTrue($result);
    }

    public function test_verify_callback_with_invalid_hash()
    {
        $callbackData = [
            'tran_id' => 'TEST_TRANSACTION_ID',
            'status' => '0',
            'amount' => '100.00',
            'currency' => 'USD',
            'req_time' => '20240101120000',
            'hash' => 'invalid_hash',
        ];

        $result = $this->service->verifyCallback($callbackData);
        $this->assertFalse($result);
    }

    public function test_get_checkout_url()
    {
        $url = $this->service->getCheckoutUrl();
        $this->assertEquals('https://checkout-sandbox.payway.com.kh/purchase', $url);
    }
}
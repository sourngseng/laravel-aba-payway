<?php

namespace SourngSeng\AbaPayway\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array createPayment(array $paymentData)
 * @method static string generateHash(array $data)
 * @method static bool verifyCallback(array $callbackData)
 * @method static bool processCallback(array $callbackData)
 * @method static \SourngSeng\AbaPayway\Models\AbaPaywayTransaction|null getTransactionStatus(string $transactionId)
 * @method static string getCheckoutUrl()
 * @method static void validatePaymentData(array $data)
 */
class AbaPayway extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'aba-payway';
    }
}
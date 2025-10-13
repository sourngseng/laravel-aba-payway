<?php

namespace SourngSeng\AbaPayway\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use SourngSeng\AbaPayway\AbaPaywayServiceProvider;

abstract class TestCase extends OrchestraTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    protected function getPackageProviders($app): array
    {
        return [
            AbaPaywayServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        $app['config']->set('aba-payway.merchant_id', 'TEST_MERCHANT_ID');
        $app['config']->set('aba-payway.merchant_secret', 'TEST_MERCHANT_SECRET');
        $app['config']->set('aba-payway.api_url', 'https://checkout-sandbox.payway.com.kh/api/payment-gateway/v1/payments/purchase');
        $app['config']->set('aba-payway.checkout_url', 'https://checkout-sandbox.payway.com.kh');
        $app['config']->set('aba-payway.sandbox', true);
    }
}
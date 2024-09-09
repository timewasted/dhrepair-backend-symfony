<?php

declare(strict_types=1);

namespace App\Tests\unit\Service\Payment\Stripe\Config;

use App\Service\Payment\Stripe\Config\PaymentIntentConfig;
use PHPUnit\Framework\TestCase;

class PaymentIntentConfigTest extends TestCase
{
    public function testToArray(): void
    {
        $config = new PaymentIntentConfig();
        $this->assertSame([], $config->toArray());

        $config
            ->setAmount(1234)
            ->setCancellationReason(PaymentIntentConfig::CANCELLATION_REASON_ABANDONED)
            ->setCardPaymentMethodOption('require_cvc_recollection', true)
            ->setCurrency(PaymentIntentConfig::CURRENCY_DEFAULT)
            ->setCustomerId('customer id')
            ->setDescription('intent description')
            ->setMetadata('bar', 'baz')
            ->setPaymentMethod('payment method')
            ->setSetupFutureUsage(PaymentIntentConfig::FUTURE_USAGE_OFF_SESSION)
        ;
        $this->assertSame([
            'amount' => 1234,
            'cancellation_reason' => PaymentIntentConfig::CANCELLATION_REASON_ABANDONED,
            'currency' => PaymentIntentConfig::CURRENCY_DEFAULT,
            'customer' => 'customer id',
            'description' => 'intent description',
            'metadata' => [
                'bar' => 'baz',
            ],
            'payment_method' => 'payment method',
            'payment_method_options' => [
                'card' => [
                    'require_cvc_recollection' => true,
                ],
            ],
            'setup_future_usage' => PaymentIntentConfig::FUTURE_USAGE_OFF_SESSION,
        ], $config->toArray());

        $config
            ->setAmount(null)
            ->setCancellationReason(null)
            ->setCardPaymentMethodOption('foo', null)
            ->setCardPaymentMethodOption('require_cvc_recollection', null)
            ->setCurrency(null)
            ->setCustomerId(null)
            ->setDescription(null)
            ->setMetadata('bar', null)
            ->setPaymentMethod(null)
            ->setSetupFutureUsage(null)
        ;
        $this->assertSame([], $config->toArray());
    }
}

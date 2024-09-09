<?php

declare(strict_types=1);

namespace App\Tests\unit\Service\Payment\Stripe\Config;

use App\Service\Payment\Stripe\Config\PaymentElementConfig;
use PHPUnit\Framework\TestCase;

class PaymentElementConfigTest extends TestCase
{
    public function testToArray(): void
    {
        $config = (new PaymentElementConfig())
            ->setEnabled(true)
            ->setFeaturePaymentMethodRedisplayEnabled(true)
            ->setFeaturePaymentMethodRemoveEnabled(false)
            ->setFeaturePaymentMethodSaveEnabled(true)
            ->setFeaturePaymentMethodSaveUsageEnabled(false)
        ;
        $this->assertSame([
            PaymentElementConfig::COMPONENT_NAME => [
                'enabled' => true,
                'features' => [
                    PaymentElementConfig::FEATURE_PAYMENT_METHOD_REDISPLAY => PaymentElementConfig::IS_ENABLED,
                    PaymentElementConfig::FEATURE_PAYMENT_METHOD_REMOVE => PaymentElementConfig::IS_DISABLED,
                    PaymentElementConfig::FEATURE_PAYMENT_METHOD_SAVE => PaymentElementConfig::IS_ENABLED,
                    PaymentElementConfig::FEATURE_PAYMENT_METHOD_SAVE_USAGE => PaymentElementConfig::IS_DISABLED,
                ],
            ],
        ], $config->toArray());
    }
}

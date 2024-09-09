<?php

declare(strict_types=1);

namespace App\Tests\unit\Service\Payment\Stripe\Config;

use App\Service\Payment\Stripe\Config\CustomerConfig;
use PHPUnit\Framework\TestCase;

class CustomerConfigTest extends TestCase
{
    public function testToArray(): void
    {
        $config = (new CustomerConfig())
            ->setEmail('test@example.com')
            ->setMetadata('order_id', '1234')
            ->setShippingAddressLine1('line 1')
            ->setShippingAddressLine2('line 2')
            ->setShippingAddressCity('Las Vegas')
            ->setShippingAddressState('NV')
            ->setShippingAddressPostalCode('12345')
            ->setShippingAddressCountry('US')
        ;
        $this->assertSame([
            'email' => 'test@example.com',
            'metadata' => [
                'order_id' => '1234',
            ],
            'shipping' => [
                'address' => [
                    'line1' => 'line 1',
                    'line2' => 'line 2',
                    'city' => 'Las Vegas',
                    'state' => 'NV',
                    'postal_code' => '12345',
                    'country' => 'US',
                ],
            ],
        ], $config->toArray());

        $config
            ->setEmail(null)
            ->setMetadata('order_id', null)
            ->setShippingAddressLine1(null)
            ->setShippingAddressLine2(null)
            ->setShippingAddressCity(null)
            ->setShippingAddressState(null)
            ->setShippingAddressPostalCode(null)
            ->setShippingAddressCountry(null)
        ;
        $this->assertSame([], $config->toArray());
    }
}

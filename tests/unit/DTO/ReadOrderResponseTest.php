<?php

declare(strict_types=1);

namespace App\Tests\unit\DTO;

use App\DTO\ReadOrderResponse;
use PHPUnit\Framework\TestCase;

class ReadOrderResponseTest extends TestCase
{
    use OrderTestTrait;

    public function testJsonSerializeWithoutItems(): void
    {
        $order = $this->createOrder();
        $dto = new ReadOrderResponse($order);

        $this->assertSame($this->getOrderData($order), $dto->jsonSerialize());
    }

    public function testJsonSerializeWithItems(): void
    {
        $order = $this->createOrder()
            ->addItem($this->createOrderItem())
            ->addItem($this->createOrderItem())
        ;
        $dto = new ReadOrderResponse($order);

        $this->assertSame($this->getOrderData($order), $dto->jsonSerialize());
    }
}

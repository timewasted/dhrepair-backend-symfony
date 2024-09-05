<?php

declare(strict_types=1);

namespace App\Tests\unit\DTO;

use App\DTO\ListOrderResponse;
use PHPUnit\Framework\TestCase;

class ListOrderResponseTest extends TestCase
{
    use OrderTestTrait;

    public function testJsonSerialize(): void
    {
        $order1 = $this->createOrder();
        $order2 = $this->createOrder();
        $dto = new ListOrderResponse([$order1, $order2]);

        $this->assertSame([
            $this->getOrderData($order1, false),
            $this->getOrderData($order2, false),
        ], $dto->jsonSerialize());
    }
}

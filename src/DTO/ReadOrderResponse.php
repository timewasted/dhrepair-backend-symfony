<?php

declare(strict_types=1);

namespace App\DTO;

use App\Entity\Order;

final readonly class ReadOrderResponse implements \JsonSerializable
{
    use OrderResponseTrait;

    private array $jsonData;

    public function __construct(Order $order)
    {
        $this->jsonData = $this->getOrderData($order);
    }

    #[\Override]
    public function jsonSerialize(): array
    {
        return $this->jsonData;
    }
}

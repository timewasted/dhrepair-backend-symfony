<?php

declare(strict_types=1);

namespace App\DTO;

use App\Entity\Order;

readonly class ListOrderResponse implements \JsonSerializable
{
    use OrderResponseTrait;

    private array $jsonData;

    /**
     * @param Order[] $orders
     */
    public function __construct(array $orders)
    {
        $jsonData = [];
        foreach ($orders as $order) {
            $jsonData[] = $this->getOrderData($order, false);
        }

        $this->jsonData = $jsonData;
    }

    public function jsonSerialize(): array
    {
        return $this->jsonData;
    }
}

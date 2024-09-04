<?php

declare(strict_types=1);

namespace App\DTO;

use App\Entity\CartItem;
use App\Entity\Item;

readonly class ReadCartResponse implements \JsonSerializable
{
    use ItemResponseTrait;

    private array $jsonData;

    /**
     * @param list<CartItem> $cartItems
     */
    public function __construct(array $cartItems)
    {
        $items = [];
        $totalCost = 0;
        foreach ($cartItems as $cartItem) {
            /** @var Item $item */
            $item = $cartItem->getItem();
            $quantity = (int) $cartItem->getQuantity();
            $quantityCost = $quantity * (int) $item->getCost();
            $totalCost += $quantityCost;

            $items[] = [
                'item' => $this->getItemData($item),
                'quantity' => $quantity,
                'quantityCost' => $quantityCost,
            ];
        }

        $this->jsonData = [
            'items' => $items,
            'totalCost' => $totalCost,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->jsonData;
    }
}

<?php

declare(strict_types=1);

namespace App\DTO;

use App\Entity\CartItem;
use App\Entity\Item;
use App\Entity\UserAuthToken;

readonly class ReadCartResponse implements \JsonSerializable
{
    use ItemResponseTrait;

    private array $jsonData;

    /**
     * @param CartItem[] $cartItems
     */
    public function __construct(array $cartItems, ?UserAuthToken $authToken = null)
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

        $jsonData = [
            'items' => $items,
            'totalCost' => $totalCost,
        ];
        if (null !== $authToken && null !== ($user = $authToken->getUser())) {
            $jsonData['account'] = [
                'user' => $user->getUserIdentifier(),
                'token' => $authToken->getAuthToken(),
            ];
        }
        $this->jsonData = $jsonData;
    }

    public function jsonSerialize(): array
    {
        return $this->jsonData;
    }
}

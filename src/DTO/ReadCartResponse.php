<?php

declare(strict_types=1);

namespace App\DTO;

use App\Entity\Item;
use App\Entity\UserAuthToken;
use App\ValueObject\ShoppingCart;

readonly class ReadCartResponse implements \JsonSerializable
{
    use ItemResponseTrait;

    private array $jsonData;

    public function __construct(ShoppingCart $shoppingCart, ?UserAuthToken $authToken = null)
    {
        $items = [];
        foreach ($shoppingCart->getCartItems() as $cartItem) {
            /** @var Item $item */
            $item = $cartItem->getItem();
            $items[] = [
                'item' => $this->getItemData($item),
                'quantity' => (int) $cartItem->getQuantity(),
                'quantityCost' => $cartItem->getQuantityCost(),
            ];
        }

        $jsonData = [
            'items' => $items,
            'totalCost' => $shoppingCart->getTotalCost(),
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

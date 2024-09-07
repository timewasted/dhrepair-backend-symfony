<?php

declare(strict_types=1);

namespace App\ValueObject;

use App\Entity\CartItem;
use App\Entity\User;

class ShoppingCart
{
    private ?int $totalCost = null;

    /**
     * @param CartItem[] $cartItems
     */
    public function __construct(
        private readonly ?User $user,
        private readonly array $cartItems,
    ) {
    }

    /**
     * @return CartItem[]
     */
    public function getCartItems(): array
    {
        return $this->cartItems;
    }

    public function getTotalCost(): int
    {
        if (null === $this->totalCost) {
            $this->totalCost = 0;
            foreach ($this->cartItems as $cartItem) {
                if (null === ($item = $cartItem->getItem())) {
                    throw new \RuntimeException('Received a cart item without a valid item attached');
                }
                $this->totalCost += (int) $cartItem->getQuantity() * (int) $item->getCost();
            }
        }

        return $this->totalCost;
    }

    /** @psalm-suppress PossiblyUnusedMethod */
    public function getUser(): ?User
    {
        return $this->user;
    }

    public function isEmpty(): bool
    {
        return empty($this->cartItems);
    }
}

<?php

declare(strict_types=1);

namespace App\Event;

use App\ValueObject\ShoppingCart;
use Symfony\Contracts\EventDispatcher\Event;

abstract class BaseCartEvent extends Event
{
    public function __construct(private readonly ShoppingCart $cart)
    {
    }

    public function getCart(): ShoppingCart
    {
        return $this->cart;
    }
}

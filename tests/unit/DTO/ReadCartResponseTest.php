<?php

declare(strict_types=1);

namespace App\Tests\unit\DTO;

use App\DTO\ReadCartResponse;
use App\Entity\CartItem;
use App\ValueObject\ShoppingCart;
use PHPUnit\Framework\TestCase;

class ReadCartResponseTest extends TestCase
{
    use ImageTestTrait;
    use ItemTestTrait;

    public function testJsonSerializeWithoutItems(): void
    {
        $dto = new ReadCartResponse(new ShoppingCart(null, []));

        $this->assertSame([
            'items' => [],
            'totalCost' => 0,
        ], $dto->jsonSerialize());
    }

    public function testJsonSerializeWithItems(): void
    {
        $image1 = $this->createImage();
        $image2 = $this->createImage();

        $item1Cost = random_int(10, 9999);
        $item1 = $this->createItem()
            ->setCost($item1Cost)
            ->setImages([$image1, $image2])
        ;
        /** @psalm-suppress PossiblyNullReference */
        $item1Cost = (int) ceil($item1Cost * (float) $item1->getManufacturer()->getCostModifier());
        $item2Cost = random_int(10, 9999);
        $item2 = $this->createItem()
            ->setCost($item2Cost)
            ->setImages([$image1, $image2])
        ;
        /** @psalm-suppress PossiblyNullReference */
        $item2Cost = (int) ceil($item2Cost * (float) $item2->getManufacturer()->getCostModifier());

        $cartItem1Quantity = random_int(1, 100);
        $cartItem1 = (new CartItem())->setItem($item1)->setQuantity($cartItem1Quantity);
        $cartItem2Quantity = random_int(1, 100);
        $cartItem2 = (new CartItem())->setItem($item2)->setQuantity($cartItem2Quantity);

        $dto = new ReadCartResponse(new ShoppingCart(null, [$cartItem1, $cartItem2]));

        $this->assertSame([
            'items' => [
                [
                    'item' => $this->getItemData($item1),
                    'quantity' => $cartItem1->getQuantity(),
                    'quantityCost' => $cartItem1Quantity * $item1Cost,
                ],
                [
                    'item' => $this->getItemData($item2),
                    'quantity' => $cartItem2->getQuantity(),
                    'quantityCost' => $cartItem2Quantity * $item2Cost,
                ],
            ],
            'totalCost' => ($cartItem1Quantity * $item1Cost) + ($cartItem2Quantity * $item2Cost),
        ], $dto->jsonSerialize());
    }
}

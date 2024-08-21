<?php

declare(strict_types=1);

namespace App\Tests\unit\DTO;

use App\DTO\ReadItemResponse;
use App\Entity\Availability;
use App\Entity\Item;
use App\Entity\Manufacturer;
use PHPUnit\Framework\TestCase;

class ReadItemResponseTest extends TestCase
{
    public function testJsonSerialize(): void
    {
        $item = $this->createItem();
        $dto = new ReadItemResponse($item);

        $this->assertSame([
            'item' => $this->relevantItemFields($item),
        ], $dto->jsonSerialize());
    }

    private function createItem(): Item
    {
        return (new Item())
            ->setName(bin2hex(random_bytes(16)))
            ->setSlug(bin2hex(random_bytes(16)))
            ->setSku(bin2hex(random_bytes(16)))
            ->setDescription(bin2hex(random_bytes(16)))
            ->setManufacturer((new Manufacturer())->setName(bin2hex(random_bytes(16))))
            ->setCost(random_int(1, PHP_INT_MAX))
            ->setQuantity(random_int(1, PHP_INT_MAX))
            ->setAvailability((new Availability())->setAvailability(bin2hex(random_bytes(16))))
            ->setWeight(bin2hex(random_bytes(16)))
            ->setLength(bin2hex(random_bytes(16)))
            ->setWidth(bin2hex(random_bytes(16)))
            ->setHeight(bin2hex(random_bytes(16)))
            ->setIsProduct(0 === random_int(0, PHP_INT_MAX) % 2)
            ->setIsViewable(0 === random_int(0, PHP_INT_MAX) % 2)
            ->setIsPurchasable(0 === random_int(0, PHP_INT_MAX) % 2)
            ->setIsSpecial(0 === random_int(0, PHP_INT_MAX) % 2)
            ->setIsNew(0 === random_int(0, PHP_INT_MAX) % 2)
            ->setChargeTax(0 === random_int(0, PHP_INT_MAX) % 2)
            ->setChargeShipping(0 === random_int(0, PHP_INT_MAX) % 2)
            ->setIsFreeShipping(0 === random_int(0, PHP_INT_MAX) % 2)
            ->setFreightQuoteRequired(0 === random_int(0, PHP_INT_MAX) % 2)
            ->setModifiedAt(new \DateTimeImmutable())
        ;
    }

    private function relevantItemFields(Item $item): array
    {
        return [
            'id' => $item->getId(),
            'name' => $item->getName(),
            'slug' => $item->getSlug(),
            'sku' => $item->getSku(),
            'description' => $item->getDescription(),
            'manufacturer' => $item->getManufacturer()?->getName(),
            'cost' => $item->getCost(),
            'quantity' => $item->getQuantity(),
            'availability' => $item->getAvailability()?->getAvailability(),
            'weight' => $item->getWeight(),
            'length' => $item->getLength(),
            'width' => $item->getWidth(),
            'height' => $item->getHeight(),
            'isProduct' => $item->isProduct(),
            'isViewable' => $item->isViewable(),
            'isPurchasable' => $item->isPurchasable(),
            'isSpecial' => $item->isSpecial(),
            'isNew' => $item->isNew(),
            'chargeTax' => $item->isChargeTax(),
            'chargeShipping' => $item->isChargeShipping(),
            'isFreeShipping' => $item->isFreeShipping(),
            'freightQuoteRequired' => $item->isFreightQuoteRequired(),
            'modifiedAt' => $item->getModifiedAt()?->format(\DateTimeInterface::ATOM),
            'images' => [],
        ];
    }
}

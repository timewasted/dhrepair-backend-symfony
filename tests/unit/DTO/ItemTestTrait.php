<?php

declare(strict_types=1);

namespace App\Tests\unit\DTO;

use App\Entity\Availability;
use App\Entity\Item;
use App\Entity\Manufacturer;

trait ItemTestTrait
{
    use ImageTestTrait;

    protected function createItem(): Item
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
            ->setIsProduct((bool) random_int(0, 1))
            ->setIsViewable((bool) random_int(0, 1))
            ->setIsPurchasable((bool) random_int(0, 1))
            ->setIsSpecial((bool) random_int(0, 1))
            ->setIsNew((bool) random_int(0, 1))
            ->setChargeTax((bool) random_int(0, 1))
            ->setChargeShipping((bool) random_int(0, 1))
            ->setIsFreeShipping((bool) random_int(0, 1))
            ->setFreightQuoteRequired((bool) random_int(0, 1))
            ->setModifiedAt(new \DateTimeImmutable())
        ;
    }

    private function getItemData(Item $item): array
    {
        $images = [];
        foreach ($item->getImages() as $image) {
            $images[] = $this->getImageData($image);
        }

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
            'images' => $images,
        ];
    }
}

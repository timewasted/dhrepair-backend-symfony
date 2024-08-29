<?php

declare(strict_types=1);

namespace App\DTO;

use App\Entity\Item;

trait ItemResponseTrait
{
    use ImageResponseTrait;

    protected function getItemData(Item $item): array
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

<?php

declare(strict_types=1);

namespace App\DTO;

use App\Entity\Item;

readonly class ReadItemResponse implements \JsonSerializable
{
    private array $jsonData;

    public function __construct(Item $item)
    {
        $this->jsonData = [
            'item' => [
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
            ],
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->jsonData;
    }
}

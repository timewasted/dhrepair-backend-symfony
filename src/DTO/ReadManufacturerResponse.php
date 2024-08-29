<?php

declare(strict_types=1);

namespace App\DTO;

use App\Entity\Manufacturer;

readonly class ReadManufacturerResponse implements \JsonSerializable
{
    private array $jsonData;

    /**
     * @param list<Manufacturer> $manufacturers
     */
    public function __construct(array $manufacturers)
    {
        $jsonData = [];
        foreach ($manufacturers as $manufacturer) {
            $jsonData[] = [
                'id' => $manufacturer->getId(),
                'name' => $manufacturer->getName(),
                'costModifier' => $manufacturer->getCostModifier()?->getModifier(),
            ];
        }
        $this->jsonData = $jsonData;
    }

    public function jsonSerialize(): mixed
    {
        return $this->jsonData;
    }
}

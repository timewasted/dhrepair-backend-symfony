<?php

declare(strict_types=1);

namespace App\DTO;

use App\Entity\Manufacturer;

final readonly class ReadManufacturerResponse implements \JsonSerializable
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
                'costModifier' => $manufacturer->getCostModifier(),
            ];
        }
        $this->jsonData = $jsonData;
    }

    #[\Override]
    public function jsonSerialize(): array
    {
        return $this->jsonData;
    }
}

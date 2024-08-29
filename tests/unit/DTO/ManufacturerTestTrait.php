<?php

declare(strict_types=1);

namespace App\Tests\unit\DTO;

use App\Entity\CostModifier;
use App\Entity\Manufacturer;

trait ManufacturerTestTrait
{
    protected function createManufacturer(?CostModifier $costModifier = null): Manufacturer
    {
        $manufacturer = (new Manufacturer())->setName(bin2hex(random_bytes(16)));
        if (null !== $costModifier) {
            $manufacturer->setCostModifier($costModifier);
        }

        return $manufacturer;
    }

    protected function getManufacturerData(Manufacturer $manufacturer): array
    {
        return [
            'id' => $manufacturer->getId(),
            'name' => $manufacturer->getName(),
            'costModifier' => $manufacturer->getCostModifier()?->getModifier(),
        ];
    }
}

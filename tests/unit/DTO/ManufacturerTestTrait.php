<?php

declare(strict_types=1);

namespace App\Tests\unit\DTO;

use App\Entity\Manufacturer;

trait ManufacturerTestTrait
{
    protected function createManufacturer(): Manufacturer
    {
        return (new Manufacturer())
            ->setName(bin2hex(random_bytes(16)))
            ->setCostModifier(sprintf('%d.%02d', random_int(0, 999), random_int(0, 99)))
        ;
    }

    protected function getManufacturerData(Manufacturer $manufacturer): array
    {
        return [
            'id' => $manufacturer->getId(),
            'name' => $manufacturer->getName(),
            'costModifier' => $manufacturer->getCostModifier(),
        ];
    }
}

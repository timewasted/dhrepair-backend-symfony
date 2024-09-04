<?php

declare(strict_types=1);

namespace App\Tests\unit\DTO;

use App\DTO\ReadManufacturerResponse;
use PHPUnit\Framework\TestCase;

class ReadManufacturerResponseTest extends TestCase
{
    use ManufacturerTestTrait;

    public function testJsonSerialize(): void
    {
        $manufacturer1 = $this->createManufacturer();
        $manufacturer2 = $this->createManufacturer();
        $dto = new ReadManufacturerResponse([$manufacturer1, $manufacturer2]);

        $this->assertSame([
            [
                'id' => $manufacturer1->getId(),
                'name' => $manufacturer1->getName(),
                'costModifier' => $manufacturer1->getCostModifier(),
            ],
            [
                'id' => $manufacturer2->getId(),
                'name' => $manufacturer2->getName(),
                'costModifier' => $manufacturer2->getCostModifier(),
            ],
        ], $dto->jsonSerialize());
    }
}

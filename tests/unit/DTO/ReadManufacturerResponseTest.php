<?php

declare(strict_types=1);

namespace App\Tests\unit\DTO;

use App\DTO\ReadManufacturerResponse;
use App\Entity\CostModifier;
use PHPUnit\Framework\TestCase;

class ReadManufacturerResponseTest extends TestCase
{
    use ManufacturerTestTrait;

    public function testJsonSerialize(): void
    {
        $manufacturer1 = $this->createManufacturer();
        $manufacturer2 = $this->createManufacturer((new CostModifier())->setModifier('1.23'));
        $dto = new ReadManufacturerResponse([$manufacturer1, $manufacturer2]);

        $this->assertSame([
            [
                'id' => $manufacturer1->getId(),
                'name' => $manufacturer1->getName(),
                'costModifier' => null,
            ],
            [
                'id' => $manufacturer2->getId(),
                'name' => $manufacturer2->getName(),
                'costModifier' => '1.23',
            ],
        ], $dto->jsonSerialize());
    }
}

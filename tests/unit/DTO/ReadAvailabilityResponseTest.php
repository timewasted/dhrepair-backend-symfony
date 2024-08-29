<?php

declare(strict_types=1);

namespace App\Tests\unit\DTO;

use App\DTO\ReadAvailabilityResponse;
use App\Entity\Availability;
use PHPUnit\Framework\TestCase;

class ReadAvailabilityResponseTest extends TestCase
{
    public function testJsonSerialize(): void
    {
        $availability1 = (new Availability())->setAvailability(bin2hex(random_bytes(16)));
        $availability2 = (new Availability())->setAvailability(bin2hex(random_bytes(16)));
        $dto = new ReadAvailabilityResponse([$availability1, $availability2]);

        $this->assertSame([
            [
                'id' => $availability1->getId(),
                'availability' => $availability1->getAvailability(),
            ],
            [
                'id' => $availability2->getId(),
                'availability' => $availability2->getAvailability(),
            ],
        ], $dto->jsonSerialize());
    }
}

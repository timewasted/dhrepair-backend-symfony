<?php

declare(strict_types=1);

namespace App\DTO;

use App\Entity\Availability;

readonly class ReadAvailabilityResponse implements \JsonSerializable
{
    private array $jsonData;

    /**
     * @param list<Availability> $availabilities
     */
    public function __construct(array $availabilities)
    {
        $jsonData = [];
        foreach ($availabilities as $availability) {
            $jsonData[] = [
                'id' => $availability->getId(),
                'availability' => $availability->getAvailability(),
            ];
        }
        $this->jsonData = $jsonData;
    }

    public function jsonSerialize(): mixed
    {
        return $this->jsonData;
    }
}

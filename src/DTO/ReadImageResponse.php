<?php

declare(strict_types=1);

namespace App\DTO;

use App\Entity\Image;

readonly class ReadImageResponse implements \JsonSerializable
{
    use ImageResponseTrait;

    private array $jsonData;

    public function __construct(Image $image)
    {
        $this->jsonData = $this->getImageData($image);
    }

    public function jsonSerialize(): array
    {
        return $this->jsonData;
    }
}

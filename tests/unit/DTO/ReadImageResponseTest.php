<?php

declare(strict_types=1);

namespace App\Tests\unit\DTO;

use App\DTO\ReadImageResponse;
use PHPUnit\Framework\TestCase;

class ReadImageResponseTest extends TestCase
{
    use ImageTestTrait;

    public function testJsonSerialize(): void
    {
        $image = $this->createImage();
        $dto = new ReadImageResponse($image);

        $this->assertSame($this->getImageData($image), $dto->jsonSerialize());
    }
}

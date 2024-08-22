<?php

declare(strict_types=1);

namespace App\Tests\unit\DTO;

use App\DTO\UpdateCategoryRequest;
use PHPUnit\Framework\TestCase;

class UpdateCategoryRequestTest extends TestCase
{
    public function testJsonSerialize(): void
    {
        $id = random_int(0, PHP_INT_MAX);
        $parentId = random_int(0, PHP_INT_MAX);
        $name = bin2hex(random_bytes(16));
        $description = bin2hex(random_bytes(16));
        $isViewable = (bool) random_int(0, 1);
        $dto = new UpdateCategoryRequest($id, $parentId, $name, $description, $isViewable);

        $this->assertSame([
            'id' => $id,
            'parentId' => $parentId,
            'name' => $name,
            'description' => $description,
            'isViewable' => $isViewable,
        ], $dto->jsonSerialize());
    }
}

<?php

declare(strict_types=1);

namespace App\Tests\unit\DTO;

use App\DTO\UpdateCategoryRequest;
use App\Entity\Category;
use PHPUnit\Framework\TestCase;

class UpdateCategoryRequestTest extends TestCase
{
    private \DateTimeImmutable $modifiedAt;

    protected function setUp(): void
    {
        $this->modifiedAt = (new \DateTimeImmutable())->sub(new \DateInterval('P1D'));
    }

    public function testUpdateEntityNoParent(): void
    {
        $id = random_int(0, PHP_INT_MAX);
        $parentId = random_int(0, PHP_INT_MAX);
        $name = bin2hex(random_bytes(16));
        $description = bin2hex(random_bytes(16));
        $isViewable = (bool) random_int(0, 1);
        $dto = new UpdateCategoryRequest($id, $parentId, $name, $description, $isViewable);

        $entity = $dto->updateEntity($this->createCategory(), null);

        $this->assertNull($entity->getParent());
        $this->assertSame($name, $entity->getName());
        $this->assertSame($description, $entity->getDescription());
        $this->assertSame($isViewable, $entity->isViewable());
        $this->assertSame($this->modifiedAt, $entity->getModifiedAt());
    }

    public function testUpdateEntity(): void
    {
        $id = random_int(0, PHP_INT_MAX);
        $parentId = random_int(0, PHP_INT_MAX);
        $name = bin2hex(random_bytes(16));
        $description = bin2hex(random_bytes(16));
        $isViewable = (bool) random_int(0, 1);
        $dto = new UpdateCategoryRequest($id, $parentId, $name, $description, $isViewable);

        $parent = $this->createCategory();
        $entity = $dto->updateEntity($this->createCategory($parent), $parent);

        $this->assertSame($parent, $entity->getParent());
        $this->assertSame($name, $entity->getName());
        $this->assertSame($description, $entity->getDescription());
        $this->assertSame($isViewable, $entity->isViewable());
        $this->assertSame($this->modifiedAt, $entity->getModifiedAt());
    }

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

    private function createCategory(?Category $parent = null): Category
    {
        return (new Category())
            ->setParent($parent)
            ->setName(bin2hex(random_bytes(16)))
            ->setSlug(bin2hex(random_bytes(16)))
            ->setDescription(bin2hex(random_bytes(16)))
            ->setIsViewable((bool) random_int(0, 1))
            ->setModifiedAt($this->modifiedAt)
        ;
    }
}

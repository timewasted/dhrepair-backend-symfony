<?php

declare(strict_types=1);

namespace App\Tests\unit\DTO;

use App\DTO\ReadCategoryResponse;
use PHPUnit\Framework\TestCase;

class ReadCategoryResponseTest extends TestCase
{
    use CategoryTestTrait;
    use ItemTestTrait;

    public function testJsonSerializeNoCategoryNoChildrenNoItems(): void
    {
        $dto = new ReadCategoryResponse(null, [], []);

        $this->assertSame([
            'category' => null,
            'parent' => null,
            'children' => [
                'categories' => [],
                'items' => [],
            ],
        ], $dto->jsonSerialize());
    }

    public function testJsonSerializeNoParentNoChildrenNoItems(): void
    {
        $category = $this->createCategory();
        $dto = new ReadCategoryResponse($category, [], []);

        $this->assertSame([
            'category' => $this->getCategoryData($category),
            'parent' => null,
            'children' => [
                'categories' => [],
                'items' => [],
            ],
        ], $dto->jsonSerialize());
    }

    public function testJsonSerializeNoChildrenNoItems(): void
    {
        $parent = $this->createCategory();
        $category = $this->createCategory($parent);
        $dto = new ReadCategoryResponse($category, [], []);

        $this->assertSame([
            'category' => $this->getCategoryData($category),
            'parent' => $this->getCategoryData($parent),
            'children' => [
                'categories' => [],
                'items' => [],
            ],
        ], $dto->jsonSerialize());
    }

    public function testJsonSerializeNoItems(): void
    {
        $category = $this->createCategory();
        $children = [
            $this->createCategory(),
            $this->createCategory(),
            $this->createCategory(),
        ];
        $dto = new ReadCategoryResponse($category, $children, []);

        $this->assertSame([
            'category' => $this->getCategoryData($category),
            'parent' => null,
            'children' => [
                'categories' => [
                    $this->getCategoryData($children[0]),
                    $this->getCategoryData($children[1]),
                    $this->getCategoryData($children[2]),
                ],
                'items' => [],
            ],
        ], $dto->jsonSerialize());
    }

    public function testJsonSerialize(): void
    {
        $category = $this->createCategory();
        $children = [
            $this->createCategory(),
            $this->createCategory(),
            $this->createCategory(),
        ];
        $items = [
            $this->createItem(),
            $this->createItem(),
            $this->createItem(),
        ];
        $dto = new ReadCategoryResponse($category, $children, $items);

        $this->assertSame([
            'category' => $this->getCategoryData($category),
            'parent' => null,
            'children' => [
                'categories' => [
                    $this->getCategoryData($children[0]),
                    $this->getCategoryData($children[1]),
                    $this->getCategoryData($children[2]),
                ],
                'items' => [
                    $this->getItemData($items[0]),
                    $this->getItemData($items[1]),
                    $this->getItemData($items[2]),
                ],
            ],
        ], $dto->jsonSerialize());
    }
}

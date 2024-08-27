<?php

declare(strict_types=1);

namespace App\Tests\unit\DTO;

use App\DTO\ReadItemResponse;
use App\Entity\Category;
use PHPUnit\Framework\TestCase;

class ReadItemResponseTest extends TestCase
{
    use CategoryTestTrait;
    use ItemTestTrait;

    public function testJsonSerializeNoPathsToRoot(): void
    {
        $item = $this->createItem();
        $dto = new ReadItemResponse($item, []);

        $this->assertSame([
            'item' => $this->getItemData($item),
            'categoryRootPaths' => [],
        ], $dto->jsonSerialize());
    }

    public function testJsonSerializeSinglePathToRoot(): void
    {
        $item = $this->createItem();
        $ancestorCategories = $this->createPathToRoot();
        $dto = new ReadItemResponse($item, [$ancestorCategories]);

        $categoryRootPath = [];
        foreach ($ancestorCategories as $category) {
            $categoryRootPath[] = $this->getCategoryData($category);
        }
        $this->assertSame([
            'item' => $this->getItemData($item),
            'categoryRootPaths' => [$categoryRootPath],
        ], $dto->jsonSerialize());
    }

    public function testJsonSerializeMultiplePathsToRoot(): void
    {
        $item = $this->createItem();
        $ancestorCategories = [
            $this->createPathToRoot(),
            $this->createPathToRoot(),
        ];
        $dto = new ReadItemResponse($item, $ancestorCategories);

        $categoryRootPaths = [];
        foreach ($ancestorCategories as $rootPath) {
            $pathData = [];
            foreach ($rootPath as $category) {
                $pathData[] = $this->getCategoryData($category);
            }
            $categoryRootPaths[] = $pathData;
        }
        $this->assertSame([
            'item' => $this->getItemData($item),
            'categoryRootPaths' => $categoryRootPaths,
        ], $dto->jsonSerialize());
    }

    /**
     * @return list<Category>
     */
    private function createPathToRoot(): array
    {
        $path = [];
        for ($i = 0; $i < 3; ++$i) {
            $path[] = $this->createCategory();
        }

        return $path;
    }
}

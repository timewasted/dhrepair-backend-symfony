<?php

declare(strict_types=1);

namespace App\DTO;

use App\Entity\Category;
use App\Entity\Item;

class ReadItemResponse implements \JsonSerializable
{
    use CategoryResponseTrait;
    use ItemResponseTrait;

    private array $jsonData;

    /**
     * @param list<list<Category>> $categoryRootPaths
     */
    public function __construct(Item $item, array $categoryRootPaths)
    {
        $this->jsonData = [
            'item' => $this->getItemData($item),
            'categoryRootPaths' => [],
        ];

        foreach ($categoryRootPaths as $categoryRootPath) {
            $pathData = [];
            foreach ($categoryRootPath as $category) {
                $pathData[] = $this->getCategoryData($category);
            }
            $this->jsonData['categoryRootPaths'][] = $pathData;
        }
    }

    public function jsonSerialize(): array
    {
        return $this->jsonData;
    }
}

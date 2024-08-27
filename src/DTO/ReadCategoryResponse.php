<?php

declare(strict_types=1);

namespace App\DTO;

use App\Entity\Category;
use App\Entity\Item;

class ReadCategoryResponse implements \JsonSerializable
{
    use CategoryResponseTrait;
    use ItemResponseTrait;

    private array $jsonData;

    /**
     * @param Category[] $children
     * @param Item[]     $items
     */
    public function __construct(?Category $category, array $children, array $items)
    {
        $parent = $category?->getParent();
        $this->jsonData = [
            'category' => null !== $category ? $this->getCategoryData($category) : null,
            'parent' => null !== $parent ? $this->getCategoryData($parent) : null,
            'children' => [
                'categories' => [],
                'items' => [],
            ],
        ];

        foreach ($children as $child) {
            $this->jsonData['children']['categories'][] = $this->getCategoryData($child);
        }

        foreach ($items as $item) {
            $this->jsonData['children']['items'][] = $this->getItemData($item);
        }
    }

    public function jsonSerialize(): array
    {
        return $this->jsonData;
    }
}

<?php

declare(strict_types=1);

namespace App\DTO;

use App\Entity\Category;
use App\Entity\Item;

class ReadCategoryResponse implements \JsonSerializable
{
    private array $jsonData;

    /**
     * @param Category[] $children
     * @param Item[]     $items
     */
    public function __construct(?Category $category, array $children, array $items)
    {
        $this->jsonData = [
            'category' => null !== $category ? $this->getRelevantData($category) : null,
            'children' => [
                'categories' => [],
                'items' => [],
            ],
        ];

        foreach ($children as $child) {
            $this->jsonData['children']['categories'][] = $this->getRelevantData($child);
        }

        foreach ($items as $item) {
            $this->jsonData['children']['items'][] = (new ReadItemResponse($item))->jsonSerialize()['item'];
        }
    }

    public function jsonSerialize(): array
    {
        return $this->jsonData;
    }

    private function getRelevantData(Category $category): array
    {
        return [
            'id' => $category->getId(),
            'name' => $category->getName(),
            'slug' => $category->getSlug(),
            'description' => $category->getDescription(),
            'isViewable' => $category->isViewable(),
            'modifiedAt' => $category->getModifiedAt()?->format(\DateTimeInterface::ATOM),
        ];
    }
}

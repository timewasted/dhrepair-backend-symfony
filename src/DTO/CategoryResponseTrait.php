<?php

declare(strict_types=1);

namespace App\DTO;

use App\Entity\Category;

trait CategoryResponseTrait
{
    protected function getCategoryData(Category $category): array
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

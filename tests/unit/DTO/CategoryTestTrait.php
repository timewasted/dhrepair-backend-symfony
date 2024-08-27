<?php

declare(strict_types=1);

namespace App\Tests\unit\DTO;

use App\Entity\Category;

trait CategoryTestTrait
{
    protected function createCategory(?Category $parent = null): Category
    {
        return (new Category())
            ->setParent($parent)
            ->setName(bin2hex(random_bytes(16)))
            ->setSlug(bin2hex(random_bytes(16)))
            ->setDescription(bin2hex(random_bytes(16)))
            ->setIsViewable((bool) random_int(0, 1))
            ->setModifiedAt(new \DateTimeImmutable())
        ;
    }

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

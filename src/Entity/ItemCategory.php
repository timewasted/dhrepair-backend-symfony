<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ItemCategoryRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ItemCategoryRepository::class)]
#[ORM\Index(name: 'category_id', fields: ['categoryId'])]
#[ORM\UniqueConstraint(name: 'item_category', fields: ['itemId', 'categoryId'])]
class ItemCategory
{
    #[ORM\Id]
    #[ORM\Column]
    #[Assert\GreaterThan(value: 0, message: 'entity.item_category.item_id.greater_than')]
    private ?int $itemId = null;

    #[ORM\Id]
    #[ORM\Column]
    #[Assert\GreaterThan(value: 0, message: 'entity.item_category.category_id.greater_than')]
    private ?int $categoryId = null;

    public function getItemId(): ?int
    {
        return $this->itemId;
    }

    public function setItemId(int $itemId): static
    {
        $this->itemId = $itemId;

        return $this;
    }

    public function getCategoryId(): ?int
    {
        return $this->categoryId;
    }

    public function setCategoryId(int $categoryId): static
    {
        $this->categoryId = $categoryId;

        return $this;
    }
}

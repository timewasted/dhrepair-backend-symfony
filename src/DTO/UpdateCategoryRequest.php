<?php

declare(strict_types=1);

namespace App\DTO;

use App\Attribute\DenormalizeEntity;
use App\Entity\Category;
use Symfony\Component\Serializer\Attribute\Context;

class UpdateCategoryRequest implements \JsonSerializable
{
    #[Context(denormalizationContext: [Category::class => ['denormalized' => true]])]
    #[DenormalizeEntity(class: Category::class, dataSource: 'id')]
    private ?Category $category = null;
    #[Context(denormalizationContext: [Category::class => ['denormalized' => true]])]
    #[DenormalizeEntity(class: Category::class, dataSource: 'parentId', nullable: true)]
    private ?Category $parent = null;

    public function __construct(
        private readonly int $id,
        private readonly ?int $parentId,
        private readonly string $name,
        private readonly string $description,
        private readonly bool $isViewable,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getParentId(): ?int
    {
        return $this->parentId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function isViewable(): bool
    {
        return $this->isViewable;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getParent(): ?Category
    {
        return $this->parent;
    }

    public function setParent(?Category $parent): static
    {
        $this->parent = $parent;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'parentId' => $this->getParentId(),
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'isViewable' => $this->isViewable(),
        ];
    }
}

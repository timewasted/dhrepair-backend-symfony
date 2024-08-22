<?php

declare(strict_types=1);

namespace App\DTO;

use App\Entity\Category;

readonly class UpdateCategoryRequest implements \JsonSerializable
{
    public function __construct(
        private int $id,
        private ?int $parentId,
        private string $name,
        private string $description,
        private bool $isViewable,
    ) {
    }

    /** @psalm-suppress PossiblyUnusedMethod */
    public function updateEntity(Category $entity, ?Category $parent): Category
    {
        return $entity
            ->setParent($parent)
            ->setName($this->getName())
            ->setDescription($this->getDescription())
            ->setIsViewable($this->isViewable())
        ;
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

<?php

declare(strict_types=1);

namespace App\DTO;

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

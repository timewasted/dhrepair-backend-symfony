<?php

declare(strict_types=1);

namespace App\Attribute;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
readonly class DenormalizeEntity
{
    public function __construct(
        /** @var class-string $class */
        private string $class,
        private string $entityId = 'id',
        private ?string $dataSource = null,
        private bool $isCollection = false,
    ) {
    }

    /**
     * @return class-string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    public function getEntityId(): string
    {
        return $this->entityId;
    }

    public function getDataSource(): ?string
    {
        return $this->dataSource;
    }

    public function isCollection(): bool
    {
        return $this->isCollection;
    }
}

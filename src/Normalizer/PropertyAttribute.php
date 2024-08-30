<?php

declare(strict_types=1);

namespace App\Normalizer;

use App\Attribute\DenormalizeEntity;

readonly class PropertyAttribute
{
    public function __construct(
        private string $name,
        private bool $isNullable,
        private DenormalizeEntity $attribute,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isNullable(): bool
    {
        return $this->isNullable;
    }

    public function getAttribute(): DenormalizeEntity
    {
        return $this->attribute;
    }
}

<?php

declare(strict_types=1);

namespace App\Attribute;

#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_METHOD)]
readonly class JsonValidation
{
    public function __construct(
        private string $schema,
        private ?string $dataPath = null,
        private ?array $globals = null,
        private ?array $slots = null
    ) {
    }

    public function getSchema(): string
    {
        return $this->schema;
    }

    public function getDataPath(): ?string
    {
        return $this->dataPath;
    }

    public function getGlobals(): ?array
    {
        return $this->globals;
    }

    public function getSlots(): ?array
    {
        return $this->slots;
    }
}

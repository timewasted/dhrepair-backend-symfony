<?php

declare(strict_types=1);

namespace App\DTO;

readonly class UpdatePageContentRequest implements \JsonSerializable
{
    public function __construct(
        private string $id,
        private ?string $title,
        private string $content,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'content' => $this->getContent(),
        ];
    }
}

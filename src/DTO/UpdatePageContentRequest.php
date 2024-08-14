<?php

declare(strict_types=1);

namespace App\DTO;

use App\Entity\PageContent;

class UpdatePageContentRequest
{
    public function __construct(
        private readonly string $id,
        private readonly ?string $title,
        private readonly string $content,
    ) {
    }

    public function updateEntity(PageContent $entity): PageContent
    {
        return $entity
            ->setTitle($this->title)
            ->setContent($this->content)
        ;
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
}

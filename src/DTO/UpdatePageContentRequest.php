<?php

declare(strict_types=1);

namespace App\DTO;

use App\Attribute\DenormalizeEntity;
use App\Entity\PageContent;
use Symfony\Component\Serializer\Attribute\Context;

class UpdatePageContentRequest implements \JsonSerializable
{
    #[Context(denormalizationContext: [PageContent::class => ['denormalized' => true]])]
    #[DenormalizeEntity(class: PageContent::class, entityId: 'page', dataSource: 'id')]
    private ?PageContent $pageContent = null;

    public function __construct(
        private readonly string $id,
        private readonly ?string $title,
        private readonly string $content,
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

    public function getPageContent(): ?PageContent
    {
        return $this->pageContent;
    }

    public function setPageContent(?PageContent $pageContent): static
    {
        $this->pageContent = $pageContent;

        return $this;
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

<?php

declare(strict_types=1);

namespace App\DTO;

use App\Entity\PageContent;

class ReadPageContentResponse implements \JsonSerializable
{
    private ?string $page;
    private ?string $title;
    private ?string $content;
    private ?\DateTimeInterface $modifiedAt;

    public function __construct(PageContent $pageContent)
    {
        $this->page = $pageContent->getPage();
        $this->title = $pageContent->getTitle();
        $this->content = $pageContent->getContent();
        $this->modifiedAt = $pageContent->getModifiedAt();
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->page,
            'title' => $this->title,
            'content' => $this->content,
            'modifiedAt' => $this->modifiedAt?->format(\DateTimeInterface::ATOM),
        ];
    }
}

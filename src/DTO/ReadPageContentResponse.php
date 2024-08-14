<?php

declare(strict_types=1);

namespace App\DTO;

use App\Entity\PageContent;

readonly class ReadPageContentResponse implements \JsonSerializable
{
    private array $jsonData;

    public function __construct(PageContent $pageContent)
    {
        $this->jsonData = [
            'id' => $pageContent->getPage(),
            'title' => $pageContent->getTitle(),
            'content' => $pageContent->getContent(),
            'modifiedAt' => $pageContent->getModifiedAt()?->format(\DateTimeInterface::ATOM),
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->jsonData;
    }
}

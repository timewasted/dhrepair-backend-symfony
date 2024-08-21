<?php

declare(strict_types=1);

namespace App\Tests\unit\DTO;

use App\DTO\ReadPageContentResponse;
use App\Entity\PageContent;
use PHPUnit\Framework\TestCase;

class ReadPageContentResponseTest extends TestCase
{
    public function testJsonSerialize(): void
    {
        $page = bin2hex(random_bytes(16));
        $title = bin2hex(random_bytes(16));
        $content = bin2hex(random_bytes(16));
        $modifiedAt = new \DateTimeImmutable();
        $pageContent = (new PageContent())
            ->setPage($page)
            ->setTitle($title)
            ->setContent($content)
            ->setModifiedAt($modifiedAt)
        ;
        $dto = new ReadPageContentResponse($pageContent);

        $this->assertSame([
            'id' => $page,
            'title' => $title,
            'content' => $content,
            'modifiedAt' => $modifiedAt->format(\DateTimeInterface::ATOM),
        ], $dto->jsonSerialize());
    }
}

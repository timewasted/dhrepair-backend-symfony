<?php

declare(strict_types=1);

namespace App\Tests\unit\DTO;

use App\DTO\UpdatePageContentRequest;
use App\Entity\PageContent;
use PHPUnit\Framework\TestCase;

class UpdatePageContentRequestTest extends TestCase
{
    public function testUpdateEntity(): void
    {
        $pageOld = bin2hex(random_bytes(16));
        $pageNew = bin2hex(random_bytes(16));
        $title = bin2hex(random_bytes(16));
        $content = bin2hex(random_bytes(16));
        $dto = new UpdatePageContentRequest($pageNew, $title, $content);

        $modifiedAt = (new \DateTimeImmutable())->sub(new \DateInterval('P1D'));
        $entity = $dto->updateEntity((new PageContent())
            ->setPage($pageOld)
            ->setTitle(bin2hex(random_bytes(16)))
            ->setContent(bin2hex(random_bytes(16)))
            ->setModifiedAt($modifiedAt)
        );

        $this->assertSame($pageOld, $entity->getPage());
        $this->assertSame($title, $entity->getTitle());
        $this->assertSame($content, $entity->getContent());
        $this->assertSame($modifiedAt, $entity->getModifiedAt());
    }

    public function testJsonSerialize(): void
    {
        $page = bin2hex(random_bytes(16));
        $title = bin2hex(random_bytes(16));
        $content = bin2hex(random_bytes(16));
        $dto = new UpdatePageContentRequest($page, $title, $content);

        $this->assertSame([
            'id' => $page,
            'title' => $title,
            'content' => $content,
        ], $dto->jsonSerialize());
    }
}

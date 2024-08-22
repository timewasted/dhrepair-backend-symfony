<?php

declare(strict_types=1);

namespace App\Tests\unit\DTO;

use App\DTO\UpdatePageContentRequest;
use PHPUnit\Framework\TestCase;

class UpdatePageContentRequestTest extends TestCase
{
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

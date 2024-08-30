<?php

declare(strict_types=1);

namespace App\Tests\unit\Normalizer\ApiRequestNormalizer;

use App\Tests\helpers\Normalizer\TestApiRequestNormalizer;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class SupportedTypeTest extends TestCase
{
    private TestApiRequestNormalizer $normalizer;

    protected function setUp(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $this->normalizer = new TestApiRequestNormalizer($entityManager);
    }

    public function testSupportedTypes(): void
    {
        $this->assertSame([
            'object' => false,
        ], $this->normalizer->getSupportedTypes(null));
        $this->assertSame([
            'object' => false,
        ], $this->normalizer->getSupportedTypes('json'));
        $this->assertSame([
            'object' => false,
        ], $this->normalizer->getSupportedTypes('xml'));
    }
}

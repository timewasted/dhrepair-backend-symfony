<?php

declare(strict_types=1);

namespace App\Tests\unit\Normalizer\ApiRequestNormalizer;

use App\Tests\helpers\Normalizer\TestApiRequestNormalizer;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class SupportsDenormalizationTest extends TestCase
{
    private TestApiRequestNormalizer $normalizer;

    protected function setUp(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $this->normalizer = new TestApiRequestNormalizer($entityManager);
    }

    public function testSupportsDenormalizationNotAnApiRequest(): void
    {
        $this->assertFalse($this->normalizer->supportsDenormalization(null, 'fooType'));
        $this->assertFalse($this->normalizer->supportsDenormalization(null, 'fooType', null, [
            'isApiRequest' => false,
        ]));
    }

    public function testSupportsDenormalizationTypeAlreadyDenormalized(): void
    {
        $this->assertFalse($this->normalizer->supportsDenormalization([], 'fooType', null, [
            'isApiRequest' => true,
            'fooType' => ['denormalized' => true],
        ]));
    }

    public function testSupportsDenormalizationAlreadyDenormalizedButTypesMatch(): void
    {
        $this->assertTrue($this->normalizer->supportsDenormalization(new \stdClass(), \stdClass::class, null, [
            'isApiRequest' => true,
            \stdClass::class => ['denormalized' => true],
        ]));
    }

    public function testSupportsDenormalization(): void
    {
        $this->assertTrue($this->normalizer->supportsDenormalization([], 'fooType', null, [
            'isApiRequest' => true,
        ]));
        $this->assertTrue($this->normalizer->supportsDenormalization([], 'fooType', null, [
            'isApiRequest' => true,
            'fooType' => ['denormalized' => false],
        ]));
    }
}

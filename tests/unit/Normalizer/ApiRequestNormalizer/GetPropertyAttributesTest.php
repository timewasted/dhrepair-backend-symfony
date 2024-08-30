<?php

declare(strict_types=1);

namespace App\Tests\unit\Normalizer\ApiRequestNormalizer;

use App\Entity\Image;
use App\Entity\Item;
use App\Entity\Manufacturer;
use App\Tests\helpers\DTO\TestApiRequestValid;
use App\Tests\helpers\Normalizer\TestApiRequestNormalizer;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class GetPropertyAttributesTest extends TestCase
{
    private TestApiRequestNormalizer $normalizer;

    protected function setUp(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $this->normalizer = new TestApiRequestNormalizer($entityManager);
    }

    public function testGetPropertyAttributesSuccess(): void
    {
        $propertyAttributes = $this->normalizer->getPropertyAttributes(TestApiRequestValid::class);
        $this->assertCount(3, $propertyAttributes);

        $this->assertArrayHasKey('item', $propertyAttributes);
        $this->assertSame(Item::class, $propertyAttributes['item']->getClass());
        $this->assertSame('id', $propertyAttributes['item']->getEntityId());
        $this->assertSame('itemId', $propertyAttributes['item']->getDataSource());
        $this->assertFalse($propertyAttributes['item']->isCollection());

        $this->assertArrayHasKey('images', $propertyAttributes);
        $this->assertSame(Image::class, $propertyAttributes['images']->getClass());
        $this->assertSame('id', $propertyAttributes['images']->getEntityId());
        $this->assertSame('imageIds', $propertyAttributes['images']->getDataSource());
        $this->assertTrue($propertyAttributes['images']->isCollection());

        $this->assertArrayHasKey('manufacturer', $propertyAttributes);
        $this->assertSame(Manufacturer::class, $propertyAttributes['manufacturer']->getClass());
        $this->assertSame('id', $propertyAttributes['manufacturer']->getEntityId());
        $this->assertNull($propertyAttributes['manufacturer']->getDataSource());
        $this->assertFalse($propertyAttributes['manufacturer']->isCollection());
    }
}

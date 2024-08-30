<?php

declare(strict_types=1);

namespace App\Tests\unit\Normalizer\ApiRequestNormalizer;

use App\Attribute\DenormalizeEntity;
use App\Entity\Availability;
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
        $this->assertCount(4, $propertyAttributes);

        $this->assertSame('item', $propertyAttributes[0]->getName());
        $this->assertFalse($propertyAttributes[0]->isNullable());
        $this->assertSame(Item::class, $propertyAttributes[0]->getAttribute()->getClass());
        $this->assertSame('id', $propertyAttributes[0]->getAttribute()->getEntityId());
        $this->assertSame('itemId', $propertyAttributes[0]->getAttribute()->getDataSource());
        $this->assertFalse($propertyAttributes[0]->getAttribute()->isCollection());

        $this->assertSame('images', $propertyAttributes[1]->getName());
        $this->assertFalse($propertyAttributes[1]->isNullable());
        $this->assertSame(Image::class, $propertyAttributes[1]->getAttribute()->getClass());
        $this->assertSame('id', $propertyAttributes[1]->getAttribute()->getEntityId());
        $this->assertSame('imageIds', $propertyAttributes[1]->getAttribute()->getDataSource());
        $this->assertTrue($propertyAttributes[1]->getAttribute()->isCollection());

        $this->assertSame('manufacturer', $propertyAttributes[2]->getName());
        $this->assertFalse($propertyAttributes[2]->isNullable());
        $this->assertSame(Manufacturer::class, $propertyAttributes[2]->getAttribute()->getClass());
        $this->assertSame('id', $propertyAttributes[2]->getAttribute()->getEntityId());
        $this->assertNull($propertyAttributes[2]->getAttribute()->getDataSource());
        $this->assertFalse($propertyAttributes[2]->getAttribute()->isCollection());

        $this->assertSame('availability', $propertyAttributes[3]->getName());
        $this->assertTrue($propertyAttributes[3]->isNullable());
        $this->assertSame(Availability::class, $propertyAttributes[3]->getAttribute()->getClass());
        $this->assertSame('id', $propertyAttributes[3]->getAttribute()->getEntityId());
        $this->assertNull($propertyAttributes[3]->getAttribute()->getDataSource());
        $this->assertFalse($propertyAttributes[3]->getAttribute()->isCollection());
    }

    public function testGetPropertyAttributesFailure(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage(sprintf('Attribute "%s" is not allowed to be repeated', DenormalizeEntity::class));
        $mockReflectionProperty = $this->createMock(\ReflectionProperty::class);
        $mockReflectionProperty->expects($this->once())->method('getAttributes')
            ->willReturn([
                new DenormalizeEntity(\stdClass::class),
                new DenormalizeEntity(\stdClass::class),
            ]);
        $mockReflectionClass = $this->createMock(\ReflectionClass::class);
        $mockReflectionClass->expects($this->once())->method('getProperties')
            ->willReturn([$mockReflectionProperty]);

        $this->normalizer->getPropertyAttributes($mockReflectionClass);
    }
}

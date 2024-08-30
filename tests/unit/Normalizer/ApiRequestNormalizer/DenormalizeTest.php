<?php

declare(strict_types=1);

namespace App\Tests\unit\Normalizer\ApiRequestNormalizer;

use App\Attribute\DenormalizeEntity;
use App\Exception\DenormalizeEntity\DataSourceNotFoundException;
use App\Exception\DenormalizeEntity\DenormalizeEntityException;
use App\Exception\DenormalizeEntity\EntityNotFoundException;
use App\Exception\DenormalizeEntity\NotACollectionException;
use App\Tests\helpers\Normalizer\TestApiRequestNormalizer;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class DenormalizeTest extends TestCase
{
    private DenormalizerInterface&MockObject $denormalizer;
    private EntityManagerInterface&MockObject $entityManager;
    private TestApiRequestNormalizer&MockObject $normalizer;
    private EntityRepository&MockObject $repository;

    protected function setUp(): void
    {
        $this->denormalizer = $this->createMock(DenormalizerInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->normalizer = $this->getMockBuilder(TestApiRequestNormalizer::class)
            ->setConstructorArgs([$this->entityManager])
            ->onlyMethods(['getPropertyAttributes'])
            ->getMock()
        ;
        $this->normalizer->setDenormalizer($this->denormalizer);
        $this->repository = $this->createMock(EntityRepository::class);
    }

    public function testDenormalizeWithoutDenormalizer(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('The denormalizer has not yet been set');
        $this->denormalizer->expects($this->never())->method('denormalize');
        $this->entityManager->expects($this->never())->method('getRepository');

        /** @psalm-suppress InvalidArgument */
        $normalizer = new TestApiRequestNormalizer($this->entityManager);
        $normalizer->denormalize([], \stdClass::class);
    }

    public function testDenormalizeTypeAlreadyDenormalized(): void
    {
        $this->denormalizer->expects($this->never())->method('denormalize');
        $this->normalizer->expects($this->never())->method('getPropertyAttributes');

        $obj = new \stdClass();
        $denormalized = $this->normalizer->denormalize($obj, \stdClass::class, null, [
            \stdClass::class => ['denormalized' => true],
        ]);
        $this->assertSame($obj, $denormalized);
    }

    public function testDenormalizeDataIsBuiltinType(): void
    {
        $this->expectException(DenormalizeEntityException::class);
        $this->expectExceptionMessage('only supports denormalizing arrays, but string was received');
        $this->denormalizer->expects($this->never())->method('denormalize');
        $this->normalizer->expects($this->never())->method('getPropertyAttributes');
        /** @var class-string $type */
        $type = 'string';

        $this->normalizer->denormalize('test', $type);
    }

    public function testDenormalizeDataIsPlainObject(): void
    {
        $this->expectException(DenormalizeEntityException::class);
        $this->expectExceptionMessage(sprintf('only supports denormalizing arrays, but %s was received', \stdClass::class));
        $this->denormalizer->expects($this->never())->method('denormalize');
        $this->normalizer->expects($this->never())->method('getPropertyAttributes');

        $this->normalizer->denormalize(new \stdClass(), \stdClass::class);
    }

    public function testDenormalizeNoPropertyAttributes(): void
    {
        $data = [];
        $type = \stdClass::class;

        $this->denormalizer->expects($this->once())->method('denormalize')
            ->with($data, $type, null, [$type => ['denormalized' => true]]);
        $this->entityManager->expects($this->never())->method('getRepository');
        $this->normalizer->expects($this->once())->method('getPropertyAttributes')->willReturn([]);

        $this->normalizer->denormalize($data, $type);
    }

    public function testDenormalizeBadDataSource(): void
    {
        $this->expectException(DataSourceNotFoundException::class);
        $this->expectExceptionMessage('Key "invalid" does not exist in $data');
        $this->denormalizer->expects($this->never())->method('denormalize');
        $this->entityManager->expects($this->never())->method('getRepository');
        $this->normalizer->expects($this->once())->method('getPropertyAttributes')
            ->willReturn([
                'foo' => new DenormalizeEntity(class: \stdClass::class, dataSource: 'invalid'),
            ]);

        $this->normalizer->denormalize(['valid' => true], \stdClass::class);
    }

    public function testDenormalizeNonCollectionEntityIterableData(): void
    {
        $this->expectException(NotACollectionException::class);
        $this->expectExceptionMessage('foo is of type array, but the entity is not flagged as being a collection');
        $this->denormalizer->expects($this->never())->method('denormalize');
        $this->entityManager->expects($this->once())->method('getRepository')
            ->with(\stdClass::class)->willReturn($this->repository);
        $this->normalizer->expects($this->once())->method('getPropertyAttributes')
            ->willReturn([
                'foo' => new DenormalizeEntity(\stdClass::class, entityId: 'foobar'),
            ]);
        $this->repository->expects($this->never())->method('findOneBy');

        $this->normalizer->denormalize(['foo' => [123]], \stdClass::class);
    }

    public function testDenormalizeNonCollectionEntityNotFound(): void
    {
        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('Unable to find an instance of stdClass where "foobar" = "123"');
        $this->denormalizer->expects($this->never())->method('denormalize');
        $this->entityManager->expects($this->once())->method('getRepository')
            ->with(\stdClass::class)->willReturn($this->repository);
        $this->normalizer->expects($this->once())->method('getPropertyAttributes')
            ->willReturn([
                'foo' => new DenormalizeEntity(\stdClass::class, entityId: 'foobar'),
            ]);
        $this->repository->expects($this->once())->method('findOneBy')->willReturn(null);

        $this->normalizer->denormalize(['foo' => 123], \stdClass::class);
    }

    public function testDenormalizeNonCollectionEntity(): void
    {
        $repoResult = new \stdClass();
        $this->denormalizer->expects($this->once())->method('denormalize')->willReturnArgument(0);
        $this->entityManager->expects($this->once())->method('getRepository')
            ->with(\stdClass::class)->willReturn($this->repository);
        $this->normalizer->expects($this->once())->method('getPropertyAttributes')
            ->willReturn([
                'foo' => new DenormalizeEntity(\stdClass::class, entityId: 'foobar'),
            ]);
        $this->repository->expects($this->once())->method('findOneBy')
            ->with(['foobar' => 123])
            ->willReturn($repoResult);

        $denormalized = $this->normalizer->denormalize(['foo' => 123], \stdClass::class);

        $this->assertSame([
            'foo' => $repoResult,
        ], $denormalized);
    }

    public function testDenormalizeCollectionEntityNotFound(): void
    {
        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('Unable to find an instance of stdClass where "foobar" = "456"');
        $this->denormalizer->expects($this->never())->method('denormalize');
        $this->entityManager->expects($this->once())->method('getRepository')
            ->with(\stdClass::class)->willReturn($this->repository);
        $this->normalizer->expects($this->once())->method('getPropertyAttributes')
            ->willReturn([
                'foo' => new DenormalizeEntity(\stdClass::class, entityId: 'foobar', isCollection: true),
            ]);
        $this->repository->expects($this->exactly(2))->method('findOneBy')
            ->with($this->callback(static function (array $criteria) {
                /** @var int $invocations */
                static $invocations = 0;

                return match (++$invocations) {
                    1 => ['foobar' => 123] === $criteria,
                    2 => ['foobar' => 456] === $criteria,
                    default => throw new \LogicException('Invalid number of invocations')
                };
            }))
            ->willReturnOnConsecutiveCalls(new \stdClass(), null);

        $this->normalizer->denormalize(['foo' => [123, 456]], \stdClass::class);
    }

    public function testDenormalizeCollectionEntity(): void
    {
        $repoResult = new \stdClass();
        $this->denormalizer->expects($this->once())->method('denormalize')->willReturnArgument(0);
        $this->entityManager->expects($this->once())->method('getRepository')
            ->with(\stdClass::class)->willReturn($this->repository);
        $this->normalizer->expects($this->once())->method('getPropertyAttributes')
            ->willReturn([
                'foo' => new DenormalizeEntity(\stdClass::class, entityId: 'foobar', isCollection: true),
            ]);
        $this->repository->expects($this->exactly(2))->method('findOneBy')
            ->with($this->callback(static function (array $criteria) {
                /** @var int $invocations */
                static $invocations = 0;

                return match (++$invocations) {
                    1 => ['foobar' => 123] === $criteria,
                    2 => ['foobar' => 456] === $criteria,
                    default => throw new \LogicException('Invalid number of invocations')
                };
            }))
            ->willReturn($repoResult);

        $denormalized = $this->normalizer->denormalize(['foo' => [123, 456]], \stdClass::class);

        $this->assertSame([
            'foo' => [$repoResult, $repoResult],
        ], $denormalized);
    }
}

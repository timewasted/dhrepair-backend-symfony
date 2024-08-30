<?php

declare(strict_types=1);

namespace App\Normalizer;

use App\Attribute\DenormalizeEntity;
use App\Exception\DenormalizeEntity\DataSourceNotFoundException;
use App\Exception\DenormalizeEntity\DenormalizeEntityException;
use App\Exception\DenormalizeEntity\EntityNotFoundException;
use App\Exception\DenormalizeEntity\NotACollectionException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class ApiRequestNormalizer implements DenormalizerInterface, DenormalizerAwareInterface
{
    private ?DenormalizerInterface $denormalizer = null;
    /**
     * @var array<string, EntityRepository>
     */
    private array $entityRepositories = [];

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * @param class-string $type
     *
     * @throws \ReflectionException
     * @throws ExceptionInterface
     *
     * @psalm-suppress MixedReturnStatement
     */
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        if (null === $this->denormalizer) {
            throw new \LogicException(sprintf('The denormalizer has not yet been set. Ensure that this class implements %s', DenormalizerAwareInterface::class));
        }

        if (isset($context[$type]['denormalized']) && true === $context[$type]['denormalized']) {
            return $data;
        }
        if (!is_array($data)) {
            $dataType = is_object($data) ? get_class($data) : gettype($data);
            throw new DenormalizeEntityException(sprintf('%s only supports denormalizing arrays, but %s was received', static::class, $dataType));
        }

        foreach ($this->getPropertyAttributes($type) as $propertyAttribute) {
            $propertyName = $propertyAttribute->getName();
            $attribute = $propertyAttribute->getAttribute();
            $dataSource = $attribute->getDataSource() ?? $propertyName;
            if (!array_key_exists($dataSource, $data)) {
                throw new DataSourceNotFoundException(sprintf('Key "%s" does not exist in $data', $dataSource));
            }
            $entityClass = $attribute->getClass();
            if (!isset($this->entityRepositories[$entityClass])) {
                $this->entityRepositories[$entityClass] = $this->entityManager->getRepository($entityClass);
            }

            $entityId = $attribute->getEntityId();
            if (is_iterable($data[$dataSource])) {
                if (!$attribute->isCollection()) {
                    $dataType = is_object($data[$dataSource]) ? get_class($data[$dataSource]) : gettype($data[$dataSource]);
                    throw new NotACollectionException(sprintf('%s is of type %s, but the entity is not flagged as being a collection', $dataSource, $dataType));
                }

                $entityData = [];
                /** @psalm-suppress MixedAssignment */
                foreach ($data[$dataSource] as $value) {
                    $entity = $this->entityRepositories[$entityClass]->findOneBy([$entityId => $value]);
                    if (null === $entity && !$propertyAttribute->isNullable()) {
                        /** @psalm-suppress MixedArgument */
                        throw new EntityNotFoundException(sprintf('Unable to find an instance of %s where "%s" = "%s"', $entityClass, $entityId, $value));
                    }
                    $entityData[] = $entity;
                }
                $data[$propertyName] = $entityData;
            } else {
                $entity = $this->entityRepositories[$entityClass]->findOneBy([$entityId => $data[$dataSource]]);
                if (null === $entity && !$propertyAttribute->isNullable()) {
                    /**
                     * @psalm-suppress MixedArgument
                     * @psalm-suppress PossiblyNullArgument
                     */
                    throw new EntityNotFoundException(sprintf('Unable to find an instance of %s where "%s" = "%s"', $entityClass, $entityId, $data[$dataSource]));
                }
                $data[$propertyName] = $entity;
            }
        }

        $context[$type] = ['denormalized' => true];

        return $this->denormalizer->denormalize($data, $type, $format, $context);
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        if ((!isset($context['isApiRequest']) || true !== $context['isApiRequest'])
            || (isset($context[$type]['denormalized']) && true === $context[$type]['denormalized'] && !($data instanceof $type))) {
            return false;
        }

        return true;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            'object' => false,
        ];
    }

    public function setDenormalizer(DenormalizerInterface $denormalizer): void
    {
        $this->denormalizer = $denormalizer;
    }

    /**
     * @param class-string|\ReflectionClass $type
     *
     * @return list<PropertyAttribute>
     *
     * @throws \ReflectionException
     */
    protected function getPropertyAttributes(mixed $type): array
    {
        $propertyAttributes = [];
        $refClass = $type instanceof \ReflectionClass ? $type : new \ReflectionClass($type);
        foreach ($refClass->getProperties() as $property) {
            $attributes = $property->getAttributes(DenormalizeEntity::class);
            $attributeCount = count($attributes);
            if ($attributeCount > 1) {
                throw new \LogicException(sprintf('Attribute "%s" is not allowed to be repeated', DenormalizeEntity::class));
            }
            if (1 === $attributeCount) {
                $propertyAttributes[] = new PropertyAttribute(
                    $property->getName(),
                    $property->getType()?->allowsNull() ?? false,
                    $attributes[0]->newInstance(),
                );
            }
        }

        return $propertyAttributes;
    }
}

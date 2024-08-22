<?php

declare(strict_types=1);

namespace App\Repository;

use App\DTO\UpdatePageContentRequest;
use App\Entity\PageContent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PageContent>
 */
class PageContentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PageContent::class);
    }

    public function update(UpdatePageContentRequest $dto): ?PageContent
    {
        $entity = $this->find($dto->getId());
        if (null !== $entity) {
            $entity = $dto->updateEntity($entity);
            $entityManager = $this->getEntityManager();
            $entityManager->persist($entity);
            $entityManager->flush();
        }

        return $entity;
    }
}

<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Category;
use App\Entity\CategoryClosure;
use App\Entity\Item;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\UnexpectedResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * @extends ServiceEntityRepository<Category>
 */
class CategoryRepository extends ServiceEntityRepository
{
    private readonly Security $security;

    public function __construct(ManagerRegistry $registry, Security $security)
    {
        parent::__construct($registry, Category::class);

        $this->security = $security;
    }

    /**
     * @return Item[]
     */
    public function getItemsInCategory(Category $category): array
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder()
            ->select('item', 'manufacturer', 'availability', 'itemImages', 'image')
            ->from(Item::class, 'item')
            ->join('item.manufacturer', 'manufacturer')
            ->join('item.availability', 'availability')
            ->join('item.categories', 'category')
            ->leftJoin('item.itemImages', 'itemImages')
            ->leftJoin('itemImages.image', 'image')
            ->where('category.id = :categoryId')
            ->orderBy('item.name', 'ASC')
            ->setParameter('categoryId', $category->getId())
        ;
        if (!$this->security->isGranted(User::ROLE_ADMIN)) {
            $queryBuilder->andWhere('item.isViewable != 0');
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @return Category[]
     *
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function getPathToCategoryRoot(Category $category): array
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder()
            ->select('category')
            ->from(Category::class, 'category')
            ->join(CategoryClosure::class, 'category_closure', 'WITH', 'category_closure.parent = category.id')
            ->where('category_closure.child = :categoryId')
            ->orderBy('category_closure.depth', 'ASC')
            ->setParameter('categoryId', $category->getId())
        ;

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @return Category[]
     */
    public function findByParent(?int $parentId): array
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder()
            ->select('category')
            ->from(CategoryClosure::class, 'closure')
            ->join(Category::class, 'category', 'WITH', 'category.id = closure.child')
            ->where('closure.depth = 0')
            ->orderBy('category.name', 'ASC')
        ;
        if (null === $parentId) {
            $queryBuilder->andWhere('category.parent IS NULL');
        } else {
            $queryBuilder
                ->andWhere('category.parent = :parentId')
                ->setParameter('parentId', $parentId)
            ;
        }
        if (!$this->security->isGranted(User::ROLE_ADMIN)) {
            $queryBuilder->andWhere('category.isViewable != 0');
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function isViewable(Category $category): bool
    {
        if ($this->security->isGranted(User::ROLE_ADMIN)) {
            return true;
        }

        $queryBuilder = $this->getEntityManager()->createQueryBuilder()
            ->select('category.isViewable')
            ->from(CategoryClosure::class, 'closure')
            ->join(Category::class, 'category', 'WITH', 'category.id = closure.parent')
            ->where('closure.child = :categoryId')
            ->orderBy('category.isViewable')
            ->setMaxResults(1)
            ->setParameter('categoryId', $category->getId())
        ;
        try {
            return (bool) $queryBuilder->getQuery()->getSingleScalarResult();
        } catch (UnexpectedResultException $e) {
            // We can safely ignore this.
        }

        return false;
    }
}

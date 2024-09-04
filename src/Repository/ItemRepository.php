<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Item;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * @extends ServiceEntityRepository<Item>
 */
class ItemRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly Security $security,
        private readonly CategoryRepository $categoryRepository,
    ) {
        parent::__construct($registry, Item::class);
    }

    /**
     * @return list<list<Category>>
     */
    public function getPathsToCategoryRoot(Item $item): array
    {
        $isAdmin = $this->security->isGranted(User::ROLE_ADMIN);
        $paths = [];
        foreach ($item->getCategories() as $category) {
            if (!$isAdmin && !$this->categoryRepository->isViewable($category)) {
                continue;
            }
            $paths[] = $this->categoryRepository->getPathToCategoryRoot($category);
        }

        return $paths;
    }

    /**
     * @param int[] $itemIds
     *
     * @return Item[]
     */
    public function getItems(array $itemIds): array
    {
        if ($this->security->isGranted(User::ROLE_ADMIN)) {
            return $this->findBy(['id' => $itemIds], ['id' => 'ASC']);
        }

        $items = $this->getEntityManager()->createQueryBuilder()
            ->select('item', 'categories')
            ->from(Item::class, 'item')
            ->join('item.categories', 'categories')
            ->where('item.id IN (:itemIds)')
            ->andWhere('item.isViewable = true')
            ->orderBy('item.id', 'ASC')
            ->setParameter('itemIds', $itemIds)
            ->getQuery()
            ->getResult()
        ;

        $validItems = [];
        foreach ($items as $item) {
            foreach ($item->getCategories() as $category) {
                if (!$this->categoryRepository->isViewable($category)) {
                    continue 2;
                }
            }
            $validItems[] = $item;
        }

        return $validItems;
    }

    public function isViewable(Item $item): bool
    {
        if (!$this->security->isGranted(User::ROLE_ADMIN) && !$item->isViewable()) {
            return false;
        }
        foreach ($item->getCategories() as $category) {
            if (!$this->categoryRepository->isViewable($category)) {
                return false;
            }
        }

        return true;
    }
}

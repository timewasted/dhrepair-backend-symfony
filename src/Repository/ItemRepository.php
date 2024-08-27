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

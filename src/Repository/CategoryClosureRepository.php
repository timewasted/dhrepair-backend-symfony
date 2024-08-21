<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\CategoryClosure;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception as DBALException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CategoryClosure>
 */
class CategoryClosureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategoryClosure::class);
    }

    public function onCategoryParentChanged(int $categoryId, int $parentId, bool $isNewCategory): void
    {
        // See: https://www.percona.com/blog/moving-subtrees-in-closure-table/
        $connection = $this->getEntityManager()->getConnection();
        try {
            $connection->beginTransaction();

            if ($isNewCategory) {
                $connection->executeStatement('
                    INSERT INTO category_closure (parent, child, depth)
                    VALUES (:categoryId, :categoryId, 0)
                ', ['categoryId' => $categoryId]);
            } else {
                $connection->executeStatement('
                    DELETE ancestor
                    FROM category_closure ancestor
                    JOIN category_closure descendant ON descendant.child = ancestor.child
                    LEFT JOIN category_closure x ON x.parent = descendant.parent AND x.child = ancestor.parent
                    WHERE descendant.parent = :categoryId AND x.parent IS NULL
                ', ['categoryId' => $categoryId]);
            }
            $connection->executeStatement('
                INSERT INTO category_closure (parent, child, depth)
                SELECT parent.parent, child.child, parent.depth + child.depth + 1
                FROM category_closure parent, category_closure child
                WHERE parent.child = :parentId AND child.parent = :categoryId
            ', [
                'categoryId' => $categoryId,
                'parentId' => $parentId,
            ]);

            $connection->commit();
        } catch (DBALException $e) {
            $connection->rollBack();
            throw $e;
        }
    }
}

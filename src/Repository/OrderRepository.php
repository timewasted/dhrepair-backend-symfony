<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Order;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * @extends ServiceEntityRepository<Order>
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly Security $security,
    ) {
        parent::__construct($registry, Order::class);
    }

    /**
     * @return Order[]
     */
    public function getOrders(): array
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder()
            ->select('orderInfo')
            ->from(Order::class, 'orderInfo')
            ->orderBy('orderInfo.id', 'ASC')
        ;
        if (!$this->security->isGranted(User::ROLE_ADMIN)) {
            $queryBuilder
                ->where('orderInfo.username = :username')
                ->setParameter('username', $this->security->getUser()->getUserIdentifier())
            ;
        }

        return $queryBuilder->getQuery()->getResult();
    }
}

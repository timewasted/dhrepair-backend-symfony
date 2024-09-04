<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\CartItem;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly Security $security,
    ) {
        parent::__construct($registry, User::class);
    }

    /**
     * @return list<CartItem>
     */
    public function getCartItems(User $user): array
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder()
            ->select('cart_item')
            ->from(CartItem::class, 'cart_item')
            ->join('cart_item.item', 'item')
            ->where('cart_item.user = :user')
            ->orderBy('item.name', 'ASC')
            ->setParameter('user', $user)
        ;
        if (!$this->security->isGranted(User::ROLE_ADMIN)) {
            $queryBuilder->andWhere('item.isViewable != 0');
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function incrementFailedLoginCount(string $username): void
    {
        $this->getEntityManager()->createQueryBuilder()
            ->update(User::class, 'u')
            ->set('u.failedLoginAttempts', 'u.failedLoginAttempts + 1')
            ->where('u.usernameCanonical = :username')
            ->setParameter('username', $username)
            ->getQuery()
            ->execute()
        ;
    }
}

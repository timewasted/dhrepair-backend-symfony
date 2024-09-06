<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\CartItem;
use App\Entity\Item;
use App\Entity\User;
use App\ValueObject\ShoppingCart;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * @extends ServiceEntityRepository<CartItem>
 */
class CartItemRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly Security $security,
        private readonly CategoryRepository $categoryRepository,
    ) {
        parent::__construct($registry, CartItem::class);
    }

    public function emptyCart(User $user): void
    {
        $this->getEntityManager()->createQueryBuilder()
            ->delete(CartItem::class, 'cartItem')
            ->where('cartItem.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->execute()
        ;
    }

    public function getShoppingCart(User $user): ShoppingCart
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder()
            ->select('cart_item', 'item')
            ->from(CartItem::class, 'cart_item')
            ->join('cart_item.item', 'item')
            ->where('cart_item.user = :user')
            ->orderBy('item.name', 'ASC')
            ->setParameter('user', $user)
        ;
        if ($this->security->isGranted(User::ROLE_ADMIN)) {
            return new ShoppingCart($user, $queryBuilder->getQuery()->getResult());
        }

        /** @var CartItem[] $cartItems */
        $cartItems = $queryBuilder
            ->addSelect('categories')
            ->join('item.categories', 'categories')
            ->andWhere('item.isViewable = true')
            ->getQuery()
            ->getResult()
        ;

        $validCartItems = [];
        foreach ($cartItems as $cartItem) {
            foreach ($cartItem->getItem()->getCategories() as $category) {
                if (!$this->categoryRepository->isViewable($category)) {
                    continue 2;
                }
            }
            $validCartItems[] = $cartItem;
        }

        return new ShoppingCart($user, $validCartItems);
    }

    /**
     * @param Item[]          $items
     * @param array<int, int> $itemQuantities
     */
    public function setCartContents(User $user, array $items, array $itemQuantities): ShoppingCart
    {
        $entityManager = $this->getEntityManager();

        foreach ($items as $item) {
            $quantity = $itemQuantities[$item->getId()] ?? 0;
            if ($quantity > 0) {
                $entityManager->persist((new CartItem())
                    ->setUser($user)
                    ->setItem($item)
                    ->setQuantity($itemQuantities[$item->getId()])
                );
            }
        }

        $entityManager->wrapInTransaction(static function (EntityManagerInterface $entityManager) use ($user): void {
            $entityManager->getConnection()->executeStatement('DELETE FROM cart_item WHERE user_id = :userId', [
                'userId' => $user->getId(),
            ]);
            $entityManager->flush();
        });

        return $this->getShoppingCart($user);
    }
}

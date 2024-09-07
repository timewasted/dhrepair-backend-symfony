<?php

declare(strict_types=1);

namespace App\Controller\API\V1\Store;

use App\Attribute\JsonValidation;
use App\DTO\ReadCartResponse;
use App\Entity\Item;
use App\Entity\User;
use App\Event\CartCreatedEvent;
use App\Event\CartDeletedEvent;
use App\Event\CartUpdatedEvent;
use App\Repository\CartItemRepository;
use App\Repository\ItemRepository;
use App\Repository\UserRepository;
use App\ValueObject\ShoppingCart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/store', name: 'store_cart_')]
class CartController extends AbstractController
{
    #[Route('/cart', name: 'read', methods: ['GET'])]
    public function read(
        #[CurrentUser] ?User $user,
        CartItemRepository $cartItemRepository,
    ): Response {
        $shoppingCart = null !== $user ? $cartItemRepository->getShoppingCart($user) : new ShoppingCart(null, []);

        return $this->json(new ReadCartResponse($shoppingCart));
    }

    #[Route('/cart', name: 'update', methods: ['PUT'])]
    #[JsonValidation(schema: '/api/v1/store/cart_update.json')]
    public function update(
        #[CurrentUser] ?User $user,
        Request $request,
        CartItemRepository $cartItemRepository,
        ItemRepository $itemRepository,
        UserRepository $userRepository,
        EventDispatcherInterface $eventDispatcher,
    ): Response {
        $authToken = null;
        if (null === $user) {
            $authToken = $userRepository->createTemporaryUser();
            /** @var User $user */
            $user = $authToken->getUser();
            $isNewCart = true;
        } else {
            $isNewCart = $user->getCartItems()->isEmpty();
        }

        /** @var array<int, int> $itemQuantities */
        $itemQuantities = $request->toArray();
        $items = $itemRepository->getItems(array_keys($itemQuantities));
        if (count($itemQuantities) !== count($items)) {
            $invalidIds = array_diff(array_keys($itemQuantities), array_map(static fn (Item $item) => $item->getId(), $items));

            return $this->json([
                'invalidItems' => array_values($invalidIds),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $shoppingCart = $cartItemRepository->setCartContents($user, $items, $itemQuantities);

        try {
            return $this->json(new ReadCartResponse($shoppingCart, $authToken));
        } finally {
            if ($shoppingCart->isEmpty()) {
                if (!$isNewCart) {
                    $eventDispatcher->dispatch(new CartDeletedEvent($shoppingCart));
                }
            } elseif ($isNewCart) {
                $eventDispatcher->dispatch(new CartCreatedEvent($shoppingCart));
            } else {
                $eventDispatcher->dispatch(new CartUpdatedEvent($shoppingCart));
            }
        }
    }

    #[Route('/cart', name: 'delete', methods: ['DELETE'])]
    public function delete(
        #[CurrentUser] User $user,
        CartItemRepository $cartItemRepository,
        EventDispatcherInterface $eventDispatcher,
    ): Response {
        $isCartEmpty = $user->getCartItems()->isEmpty();
        if (!$isCartEmpty) {
            $cartItemRepository->emptyCart($user);
        }
        $shoppingCart = new ShoppingCart($user, []);

        try {
            return $this->json(new ReadCartResponse($shoppingCart));
        } finally {
            if (!$isCartEmpty) {
                $eventDispatcher->dispatch(new CartDeletedEvent($shoppingCart));
            }
        }
    }
}

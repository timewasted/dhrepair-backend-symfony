<?php

declare(strict_types=1);

namespace App\Controller\API\V1\Store;

use App\Attribute\JsonValidation;
use App\DTO\ReadCartResponse;
use App\Entity\Item;
use App\Entity\User;
use App\Repository\CartItemRepository;
use App\Repository\ItemRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        $cartItems = null !== $user ? $cartItemRepository->getCartItems($user) : [];

        return $this->json(new ReadCartResponse($cartItems));
    }

    #[Route('/cart', name: 'update', methods: ['PUT'])]
    #[JsonValidation(schema: '/api/v1/store/cart_update.json')]
    public function update(
        #[CurrentUser] ?User $user,
        Request $request,
        CartItemRepository $cartItemRepository,
        ItemRepository $itemRepository,
        UserRepository $userRepository,
    ): Response {
        $authToken = null;
        if (null === $user) {
            $authToken = $userRepository->createTemporaryUser();
            /** @var User $user */
            $user = $authToken->getUser();
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
        $cartItemRepository->setCartContents($user, $items, $itemQuantities);

        return $this->json(new ReadCartResponse($cartItemRepository->getCartItems($user), $authToken));
    }

    #[Route('/cart', name: 'delete', methods: ['DELETE'])]
    public function delete(
        #[CurrentUser] User $user,
        CartItemRepository $cartItemRepository,
    ): Response {
        $cartItemRepository->emptyCart($user);

        return $this->json(new ReadCartResponse([]));
    }
}

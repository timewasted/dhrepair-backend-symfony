<?php

declare(strict_types=1);

namespace App\Controller\API\V1\Store;

use App\DTO\ReadCartResponse;
use App\Entity\User;
use App\Repository\CartItemRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/store', name: 'store_cart_')]
class CartController extends AbstractController
{
    #[Route('/cart', name: 'read', methods: ['GET'])]
    public function read(
        #[CurrentUser] ?User $user,
        UserRepository $userRepository,
    ): Response {
        $cartItems = null !== $user ? $userRepository->getCartItems($user) : [];

        return $this->json(new ReadCartResponse($cartItems));
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

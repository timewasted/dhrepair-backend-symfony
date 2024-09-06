<?php

declare(strict_types=1);

namespace App\Tests\functional\Controller\API\V1\Store\Cart;

use App\DataFixtures\ShoppingFixtures;
use App\DTO\ReadCartResponse;
use App\Entity\CartItem;
use App\Entity\Item;
use App\Entity\User;
use App\Entity\UserAuthToken;
use App\Repository\CartItemRepository;
use App\Repository\ItemRepository;
use App\Repository\UserAuthTokenRepository;
use App\Repository\UserRepository;
use App\Tests\traits\ApiRequestTrait;
use App\ValueObject\ShoppingCart;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UpdateCartTest extends WebTestCase
{
    use ApiRequestTrait;

    private const string UPDATE_URL = '/api/v1/store/cart';

    private KernelBrowser $client;
    private CartItemRepository $cartItemRepository;
    private ItemRepository $itemRepository;
    private UserRepository $userRepository;
    private UserAuthTokenRepository $userAuthTokenRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->catchExceptions(false);

        $container = self::getContainer();
        $entityManager = $container->get('doctrine')->getManager();

        $this->cartItemRepository = $entityManager->getRepository(CartItem::class);
        $this->itemRepository = $entityManager->getRepository(Item::class);
        $this->userRepository = $entityManager->getRepository(User::class);
        $this->userAuthTokenRepository = $entityManager->getRepository(UserAuthToken::class);
    }

    /**
     * @return list<array{string}>
     */
    public static function providerUsername(): array
    {
        return [
            ['temporary_user'],
            ['valid_user'],
            ['admin_user'],
            ['super_admin_user'],
        ];
    }

    /**
     * @return list<array{string, bool}>
     */
    public static function providerUsernameAndCanSeeHidden(): array
    {
        return [
            ['temporary_user', false],
            ['valid_user', false],
            ['admin_user', true],
            ['super_admin_user', true],
        ];
    }

    public function testUpdateUnauthenticatedInvalidItemIds(): void
    {
        /** @var array<int, int> $itemQuantities */
        $itemQuantities = [
            ShoppingFixtures::ITEM_ID_EVERYTHING_VIEWABLE => 3,
            ShoppingFixtures::ITEM_ID_ANCESTOR_NOT_VIEWABLE => 2,
            ShoppingFixtures::ITEM_ID_NOT_VIEWABLE => 1,
        ];
        $this->makeApiRequest('PUT', self::UPDATE_URL, null, $itemQuantities);

        $jsonData = $this->getJsonData(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertArrayHasKey('invalidItems', $jsonData);
        $this->assertIsArray($jsonData['invalidItems']);

        $invalidIds = $jsonData['invalidItems'];
        sort($invalidIds);
        $this->assertSame([
            ShoppingFixtures::ITEM_ID_NOT_VIEWABLE,
            ShoppingFixtures::ITEM_ID_ANCESTOR_NOT_VIEWABLE,
        ], $invalidIds);
    }

    public function testUpdateUnauthenticated(): void
    {
        /** @var array<int, int> $itemQuantities */
        $itemQuantities = [
            ShoppingFixtures::ITEM_ID_EVERYTHING_VIEWABLE => 3,
        ];
        $this->makeApiRequest('PUT', self::UPDATE_URL, null, $itemQuantities);

        $this->validateCartResponse($itemQuantities, null);
    }

    /**
     * @dataProvider providerUsernameAndCanSeeHidden
     */
    public function testUpdateAuthenticatedInvalidItemIds(string $username, bool $canSeeHidden): void
    {
        /** @var User $user */
        $user = $this->userRepository->findOneBy(['usernameCanonical' => $username]);
        if ($canSeeHidden) {
            /** @var array<int, int> $itemQuantities */
            $itemQuantities = [
                999999 => 4,
                ShoppingFixtures::ITEM_ID_EVERYTHING_VIEWABLE => 3,
                ShoppingFixtures::ITEM_ID_ANCESTOR_NOT_VIEWABLE => 2,
                ShoppingFixtures::ITEM_ID_NOT_VIEWABLE => 1,
            ];
        } else {
            /** @var array<int, int> $itemQuantities */
            $itemQuantities = [
                ShoppingFixtures::ITEM_ID_EVERYTHING_VIEWABLE => 3,
                ShoppingFixtures::ITEM_ID_ANCESTOR_NOT_VIEWABLE => 2,
                ShoppingFixtures::ITEM_ID_NOT_VIEWABLE => 1,
            ];
        }
        $this->makeApiRequest('PUT', self::UPDATE_URL, null, $itemQuantities, $user);

        $jsonData = $this->getJsonData(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertArrayHasKey('invalidItems', $jsonData);
        $this->assertIsArray($jsonData['invalidItems']);

        $invalidIds = $jsonData['invalidItems'];
        sort($invalidIds);
        if ($canSeeHidden) {
            $this->assertSame([999999], $invalidIds);
        } else {
            $this->assertSame([
                ShoppingFixtures::ITEM_ID_NOT_VIEWABLE,
                ShoppingFixtures::ITEM_ID_ANCESTOR_NOT_VIEWABLE,
            ], $invalidIds);
        }
    }

    /**
     * @dataProvider providerUsernameAndCanSeeHidden
     */
    public function testUpdateAuthenticated(string $username, bool $canSeeHidden): void
    {
        /** @var User $user */
        $user = $this->userRepository->findOneBy(['usernameCanonical' => $username]);
        if ($canSeeHidden) {
            /** @var array<int, int> $itemQuantities */
            $itemQuantities = [
                ShoppingFixtures::ITEM_ID_ANCESTOR_NOT_VIEWABLE => 2,
                ShoppingFixtures::ITEM_ID_EVERYTHING_VIEWABLE => 3,
                ShoppingFixtures::ITEM_ID_NOT_VIEWABLE => 1,
            ];
        } else {
            /** @var array<int, int> $itemQuantities */
            $itemQuantities = [
                ShoppingFixtures::ITEM_ID_EVERYTHING_VIEWABLE => 3,
            ];
        }

        $this->makeApiRequest('PUT', self::UPDATE_URL, null, $itemQuantities, $user);

        $this->validateCartResponse($itemQuantities, $user);
    }

    private function createCartItem(User $user, int $itemId, int $quantity): CartItem
    {
        return (new CartItem())
            ->setUser($user)
            ->setItem($this->itemRepository->find($itemId))
            ->setQuantity($quantity)
        ;
    }

    /**
     * @param array<int, int> $itemQuantities
     */
    private function validateCartResponse(array $itemQuantities, ?User $user): void
    {
        $jsonData = $this->getJsonData();

        if (null === $user) {
            $this->assertArrayHasKey('account', $jsonData);
            $this->assertIsArray($jsonData['account']);
            $this->assertArrayHasKey('user', $jsonData['account']);
            $this->assertArrayHasKey('token', $jsonData['account']);

            $authToken = $this->userAuthTokenRepository->find($jsonData['account']['token']);
            $this->assertNotNull($authToken);
            $user = $authToken->getUser();
            $this->assertNotNull($user);
            $this->assertSame($user->getUserIdentifier(), $jsonData['account']['user']);
        } else {
            $this->assertArrayNotHasKey('account', $jsonData);
            $authToken = null;
        }

        /** @var CartItem[] $cartItems */
        $cartItems = [];
        foreach ($itemQuantities as $itemId => $quantity) {
            $cartItems[] = $this->createCartItem($user, $itemId, $quantity);
        }
        $shoppingCart = new ShoppingCart($user, $cartItems);
        $this->assertSame((new ReadCartResponse($shoppingCart, $authToken))->jsonSerialize(), $jsonData);

        $storedCartItems = $this->cartItemRepository->findBy(['user' => $user]);
        $this->assertSame(count($cartItems), count($storedCartItems));
        $sortFunc = static function (CartItem $a, CartItem $b): int {
            /** @var Item $itemA */
            $itemA = $a->getItem();
            /** @var Item $itemB */
            $itemB = $b->getItem();
            if ($itemA->getId() === $itemB->getId()) {
                return 0;
            }

            return $itemA->getId() <=> $itemB->getId();
        };
        usort($cartItems, $sortFunc);
        usort($storedCartItems, $sortFunc);

        foreach ($cartItems as $index => $cartItem) {
            /** @psalm-suppress PossiblyNullReference */
            $this->assertSame($cartItem->getItem()->getId(), $storedCartItems[$index]->getItem()->getId());
        }
    }

    private function getJsonData(int $expectedResponse = Response::HTTP_OK): array
    {
        $this->assertResponseStatusCodeSame($expectedResponse);
        $response = $this->client->getResponse();
        $this->assertJson((string) $response->getContent());

        return (array) json_decode((string) $response->getContent(), true);
    }
}

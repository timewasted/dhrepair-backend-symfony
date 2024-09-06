<?php

declare(strict_types=1);

namespace App\Tests\functional\Controller\API\V1\Store\Cart;

use App\DataFixtures\ShoppingFixtures;
use App\DTO\ReadCartResponse;
use App\Entity\CartItem;
use App\Entity\Item;
use App\Entity\User;
use App\Repository\ItemRepository;
use App\Repository\UserRepository;
use App\Tests\traits\ApiRequestTrait;
use App\ValueObject\ShoppingCart;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ReadCartTest extends WebTestCase
{
    use ApiRequestTrait;

    private const string READ_URL = '/api/v1/store/cart';

    private KernelBrowser $client;
    private ItemRepository $itemRepository;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->catchExceptions(false);

        $container = self::getContainer();
        $entityManager = $container->get('doctrine')->getManager();

        $this->itemRepository = $entityManager->getRepository(Item::class);
        $this->userRepository = $entityManager->getRepository(User::class);
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
     * @return list<array{string, bool, bool}>
     */
    public static function providerUsernameAndCanSeeHiddenAndHasCart(): array
    {
        return [
            ['temporary_user', false, true],
            ['valid_user', false, true],
            ['admin_user', true, true],
            ['super_admin_user', true, false],
        ];
    }

    public function testReadUnauthenticated(): void
    {
        $dto = new ReadCartResponse(new ShoppingCart(null, []));
        $this->makeApiRequest('GET', self::READ_URL);

        $this->assertSame($dto->jsonSerialize(), $this->getJsonData());
    }

    /**
     * @dataProvider providerUsernameAndCanSeeHiddenAndHasCart
     */
    public function testReadAuthenticated(string $username, bool $canSeeHidden, bool $hasCart): void
    {
        $user = $this->userRepository->findOneBy(['usernameCanonical' => $username]);
        $this->assertNotNull($user);

        if (!$hasCart) {
            $cartItems = [];
        } elseif (!$canSeeHidden) {
            $cartItems = [
                $this->createCartItem($user, ShoppingFixtures::ITEM_ID_EVERYTHING_VIEWABLE, 3),
            ];
        } else {
            $cartItems = [
                $this->createCartItem($user, ShoppingFixtures::ITEM_ID_ANCESTOR_NOT_VIEWABLE, 1),
                $this->createCartItem($user, ShoppingFixtures::ITEM_ID_EVERYTHING_VIEWABLE, 3),
                $this->createCartItem($user, ShoppingFixtures::ITEM_ID_NOT_VIEWABLE, 2),
            ];
        }
        $dto = new ReadCartResponse(new ShoppingCart($user, $cartItems));
        $this->makeApiRequest('GET', self::READ_URL, null, null, $user);

        $this->assertSame($dto->jsonSerialize(), $this->getJsonData());
    }

    private function createCartItem(User $user, int $itemId, int $quantity): CartItem
    {
        return (new CartItem())
            ->setUser($user)
            ->setItem($this->itemRepository->find($itemId))
            ->setQuantity($quantity)
        ;
    }

    private function getJsonData(): array
    {
        $this->assertResponseIsSuccessful();
        $response = $this->client->getResponse();
        $this->assertJson((string) $response->getContent());

        return (array) json_decode((string) $response->getContent(), true);
    }
}

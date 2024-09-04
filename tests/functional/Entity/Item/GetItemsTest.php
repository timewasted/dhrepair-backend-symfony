<?php

declare(strict_types=1);

namespace App\Tests\functional\Entity\Item;

use App\DataFixtures\ShoppingFixtures;
use App\DataFixtures\UserFixtures;
use App\Entity\Item;
use App\Entity\User;
use App\Repository\ItemRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GetItemsTest extends WebTestCase
{
    private const string AUTH_URL = '/api/v1/security/auth-token';

    private KernelBrowser $client;
    private ItemRepository $itemRepository;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $container = self::getContainer();

        $entityManager = $container->get('doctrine')->getManager();
        $this->itemRepository = $entityManager->getRepository(Item::class);
        $this->userRepository = $entityManager->getRepository(User::class);
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

    public function testGetItemsUnauthenticated(): void
    {
        $items = $this->itemRepository->getItems([
            ShoppingFixtures::ITEM_ID_EVERYTHING_VIEWABLE,
            ShoppingFixtures::ITEM_ID_ANCESTOR_NOT_VIEWABLE,
            ShoppingFixtures::ITEM_ID_NOT_VIEWABLE,
            999999,
        ]);
        /** @var list<Item> $expectedItems */
        $expectedItems = [
            $this->itemRepository->find(ShoppingFixtures::ITEM_ID_EVERYTHING_VIEWABLE),
        ];

        $this->assertSame($expectedItems, $items);
    }

    /**
     * @dataProvider providerUsernameAndCanSeeHidden
     */
    public function testGetItemsAuthenticated(string $username, bool $canSeeHidden): void
    {
        $this->authenticate($username);
        $items = $this->itemRepository->getItems([
            ShoppingFixtures::ITEM_ID_EVERYTHING_VIEWABLE,
            ShoppingFixtures::ITEM_ID_ANCESTOR_NOT_VIEWABLE,
            ShoppingFixtures::ITEM_ID_NOT_VIEWABLE,
            999999,
        ]);

        if ($canSeeHidden) {
            /** @var list<Item> $expectedItems */
            $expectedItems = [
                $this->itemRepository->find(ShoppingFixtures::ITEM_ID_EVERYTHING_VIEWABLE),
                $this->itemRepository->find(ShoppingFixtures::ITEM_ID_NOT_VIEWABLE),
                $this->itemRepository->find(ShoppingFixtures::ITEM_ID_ANCESTOR_NOT_VIEWABLE),
            ];
        } else {
            /** @var list<Item> $expectedItems */
            $expectedItems = [
                $this->itemRepository->find(ShoppingFixtures::ITEM_ID_EVERYTHING_VIEWABLE),
            ];
        }

        $this->assertSame($expectedItems, $items);
    }

    private function authenticate(string $username): void
    {
        $this->client->jsonRequest('POST', self::AUTH_URL, [
            'username' => $username,
            'password' => UserFixtures::DEFAULT_PASSWORD,
        ]);

        $this->assertResponseIsSuccessful();
    }
}

<?php

declare(strict_types=1);

namespace App\Tests\functional\Controller\API\V1\Store\Item;

use App\DTO\ReadItemResponse;
use App\Entity\Category;
use App\Entity\Item;
use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\ItemRepository;
use App\Repository\UserRepository;
use App\Tests\traits\ApiRequestTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ReadItemTest extends WebTestCase
{
    use ApiRequestTrait;

    private const string READ_URL = '/api/v1/store/item/';

    private KernelBrowser $client;
    private CategoryRepository $categoryRepository;
    private ItemRepository $itemRepository;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->catchExceptions(false);

        $container = self::getContainer();
        $entityManager = $container->get('doctrine')->getManager();

        $this->categoryRepository = $entityManager->getRepository(Category::class);
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

    public function testReadUnauthenticatedItemDoesNotExist(): void
    {
        $itemId = 9999;
        $this->makeApiRequest('GET', self::READ_URL.$itemId);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testReadUnauthenticatedItemIsNotViewable(): void
    {
        $itemId = 6;
        /** @var Item $item */
        $item = $this->itemRepository->find($itemId);
        $this->assertFalse($item->isViewable());

        $this->makeApiRequest('GET', self::READ_URL.$itemId);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testReadUnauthenticatedAncestorCategoryIsNotViewable(): void
    {
        $itemId = 112;
        /** @var Item $item */
        $item = $this->itemRepository->find($itemId);
        $this->assertTrue($item->isViewable());
        $this->assertFalse($this->itemRepository->isViewable($item));

        $this->makeApiRequest('GET', self::READ_URL.$itemId);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testReadUnauthenticated(): void
    {
        $itemId = 4;
        /** @var Item $item */
        $item = $this->itemRepository->find($itemId);
        $this->assertTrue($item->isViewable());
        $this->assertTrue($this->itemRepository->isViewable($item));
        /** @var list<list<Category>> $ancestorCategories */
        $ancestorCategories = [
            [
                $this->categoryRepository->find(3),
                $this->categoryRepository->find(2),
                $this->categoryRepository->find(1),
            ],
        ];
        $dto = new ReadItemResponse($item, $ancestorCategories);

        $this->doItemReadTest($itemId, null, $dto);
    }

    /**
     * @dataProvider providerUsername
     */
    public function testReadAuthenticatedItemDoesNotExist(string $username): void
    {
        $user = $this->userRepository->findOneBy(['usernameCanonical' => $username]);
        $itemId = 9999;
        $this->makeApiRequest('GET', self::READ_URL.$itemId, null, null, $user);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    /**
     * @dataProvider providerUsernameAndCanSeeHidden
     */
    public function testReadAuthenticatedItemIsNotViewable(string $username, bool $canSeeHidden): void
    {
        $user = $this->userRepository->findOneBy(['usernameCanonical' => $username]);
        $itemId = 6;
        /** @var Item $item */
        $item = $this->itemRepository->find($itemId);
        $this->assertFalse($item->isViewable());

        if (!$canSeeHidden) {
            $this->makeApiRequest('GET', self::READ_URL.$itemId, null, null, $user);
            $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        } else {
            /** @var list<list<Category>> $ancestorCategories */
            $ancestorCategories = [
                [
                    $this->categoryRepository->find(3),
                    $this->categoryRepository->find(2),
                    $this->categoryRepository->find(1),
                ],
            ];
            $dto = new ReadItemResponse($item, $ancestorCategories);
            $this->doItemReadTest($itemId, $user, $dto);
        }
    }

    /**
     * @dataProvider providerUsernameAndCanSeeHidden
     */
    public function testReadAuthenticatedAncestorCategoryIsNotViewable(string $username, bool $canSeeHidden): void
    {
        $user = $this->userRepository->findOneBy(['usernameCanonical' => $username]);
        $itemId = 112;
        /** @var Item $item */
        $item = $this->itemRepository->find($itemId);
        $this->assertTrue($item->isViewable());
        /** @var Category $category */
        $category = $this->categoryRepository->find(40);
        $this->assertFalse($category->isViewable());

        if (!$canSeeHidden) {
            $this->makeApiRequest('GET', self::READ_URL.$itemId, null, null, $user);
            $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        } else {
            /** @var list<list<Category>> $ancestorCategories */
            $ancestorCategories = [
                [
                    $this->categoryRepository->find(42),
                    $this->categoryRepository->find(41),
                    $this->categoryRepository->find(40),
                ],
            ];
            $dto = new ReadItemResponse($item, $ancestorCategories);
            $this->doItemReadTest($itemId, $user, $dto);
        }
    }

    /**
     * @dataProvider providerUsernameAndCanSeeHidden
     */
    public function testReadAuthenticated(string $username, bool $canSeeHidden): void
    {
        $user = $this->userRepository->findOneBy(['usernameCanonical' => $username]);
        $itemId = 4;
        /** @var Item $item */
        $item = $this->itemRepository->find($itemId);
        $this->assertTrue($item->isViewable());
        $this->assertTrue($this->itemRepository->isViewable($item));
        /** @var list<list<Category>> $ancestorCategories */
        $ancestorCategories = [
            [
                $this->categoryRepository->find(3),
                $this->categoryRepository->find(2),
                $this->categoryRepository->find(1),
            ],
        ];
        $dto = new ReadItemResponse($item, $ancestorCategories);

        $this->doItemReadTest($itemId, $user, $dto);
    }

    private function doItemReadTest(int $itemId, ?User $user, ReadItemResponse $dto): void
    {
        $this->makeApiRequest('GET', self::READ_URL.$itemId, null, null, $user);

        $this->assertResponseIsSuccessful();
        $response = $this->client->getResponse();
        $this->assertJson((string) $response->getContent());
        $jsonData = (array) json_decode((string) $response->getContent(), true);

        $this->assertSame($dto->jsonSerialize(), $jsonData);
    }
}

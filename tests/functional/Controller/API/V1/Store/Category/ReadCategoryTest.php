<?php

declare(strict_types=1);

namespace App\Tests\functional\Controller\API\V1\Store\Category;

use App\DTO\ReadCategoryResponse;
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

class ReadCategoryTest extends WebTestCase
{
    use ApiRequestTrait;

    private const string READ_URL = '/api/v1/store/category/';

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

    public function testReadUnauthenticatedCategoryExistsNoItems(): void
    {
        $categoryId = 1;
        $category = $this->categoryRepository->find($categoryId);
        /** @var Category[] $childCategories */
        $childCategories = [
            $this->categoryRepository->find(2),
            $this->categoryRepository->find(6),
        ];
        $dto = new ReadCategoryResponse($category, $childCategories, []);

        $this->doCategoryReadTest($categoryId, null, $dto);
    }

    public function testReadUnauthenticatedCategoryExistsWithItems(): void
    {
        $categoryId = 2;
        $category = $this->categoryRepository->find($categoryId);
        /** @var Category[] $childCategories */
        $childCategories = [
            $this->categoryRepository->find(3),
            $this->categoryRepository->find(4),
        ];
        /** @var Item[] $items */
        $items = [
            $this->itemRepository->find(1),
            $this->itemRepository->find(2),
        ];
        $dto = new ReadCategoryResponse($category, $childCategories, $items);

        $this->doCategoryReadTest($categoryId, null, $dto);
    }

    public function testReadUnauthenticatedCategoryDoesNotExist(): void
    {
        $categoryId = 9999;
        $this->makeApiRequest('GET', self::READ_URL.$categoryId);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testReadUnauthenticatedCategoryIsNotViewable(): void
    {
        $categoryId = 5;
        $this->makeApiRequest('GET', self::READ_URL.$categoryId);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testReadUnauthenticatedCategoryIsViewableButAncestorIsNot(): void
    {
        $categoryId = 11;
        $this->makeApiRequest('GET', self::READ_URL.$categoryId);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    /**
     * @dataProvider providerUsernameAndCanSeeHidden
     */
    public function testReadAuthenticatedCategoryExistsNoItems(string $username, bool $canSeeHidden): void
    {
        $user = $this->userRepository->findOneBy(['usernameCanonical' => $username]);
        $categoryId = 1;
        $category = $this->categoryRepository->find($categoryId);
        $childCategories = [
            $this->categoryRepository->find(2),
            $this->categoryRepository->find(6),
        ];
        if ($canSeeHidden) {
            $childCategories = array_merge([$this->categoryRepository->find(10)], $childCategories);
        }
        /** @var Category[] $childCategories */
        $dto = new ReadCategoryResponse($category, $childCategories, []);

        $this->doCategoryReadTest($categoryId, $user, $dto);
    }

    /**
     * @dataProvider providerUsernameAndCanSeeHidden
     */
    public function testReadAuthenticatedCategoryExistsWithItems(string $username, bool $canSeeHidden): void
    {
        $user = $this->userRepository->findOneBy(['usernameCanonical' => $username]);
        $categoryId = 2;
        $category = $this->categoryRepository->find($categoryId);
        $childCategories = [
            $this->categoryRepository->find(3),
            $this->categoryRepository->find(4),
        ];
        $items = [
            $this->itemRepository->find(1),
            $this->itemRepository->find(2),
        ];
        if ($canSeeHidden) {
            $childCategories[] = $this->categoryRepository->find(5);
            $items[] = $this->itemRepository->find(3);
        }
        /**
         * @var Category[] $childCategories
         * @var Item[]     $items
         */
        $dto = new ReadCategoryResponse($category, $childCategories, $items);

        $this->doCategoryReadTest($categoryId, $user, $dto);
    }

    /**
     * @dataProvider providerUsername
     */
    public function testReadAuthenticatedCategoryDoesNotExist(string $username): void
    {
        $user = $this->userRepository->findOneBy(['usernameCanonical' => $username]);
        $categoryId = 9999;
        $this->makeApiRequest('GET', self::READ_URL.$categoryId, null, null, $user);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    /**
     * @dataProvider providerUsernameAndCanSeeHidden
     */
    public function testReadAuthenticatedCategoryIsNotViewable(string $username, bool $canSeeHidden): void
    {
        $user = $this->userRepository->findOneBy(['usernameCanonical' => $username]);
        $categoryId = 5;
        if ($canSeeHidden) {
            $category = $this->categoryRepository->find($categoryId);
            /** @var Item[] $items */
            $items = [
                $this->itemRepository->find(10),
                $this->itemRepository->find(11),
                $this->itemRepository->find(12),
            ];
            $dto = new ReadCategoryResponse($category, [], $items);

            $this->doCategoryReadTest($categoryId, $user, $dto);
        } else {
            $this->makeApiRequest('GET', self::READ_URL.$categoryId, null, null, $user);

            $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @dataProvider providerUsernameAndCanSeeHidden
     */
    public function testReadAuthenticatedCategoryIsViewableButAncestorIsNot(string $username, bool $canSeeHidden): void
    {
        $user = $this->userRepository->findOneBy(['usernameCanonical' => $username]);
        $categoryId = 11;
        if ($canSeeHidden) {
            $category = $this->categoryRepository->find($categoryId);
            /** @var Item[] $items */
            $items = [
                $this->itemRepository->find(28),
                $this->itemRepository->find(29),
                $this->itemRepository->find(30),
            ];
            $dto = new ReadCategoryResponse($category, [], $items);

            $this->doCategoryReadTest($categoryId, $user, $dto);
        } else {
            $this->makeApiRequest('GET', self::READ_URL.$categoryId, null, null, $user);

            $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        }
    }

    private function doCategoryReadTest(int $categoryId, ?User $user, ReadCategoryResponse $dto): void
    {
        $this->makeApiRequest('GET', self::READ_URL.$categoryId, null, null, $user);

        $this->assertResponseIsSuccessful();
        $response = $this->client->getResponse();
        $this->assertJson((string) $response->getContent());
        $jsonData = (array) json_decode((string) $response->getContent(), true);

        $this->assertSame($dto->jsonSerialize(), $jsonData);
    }
}

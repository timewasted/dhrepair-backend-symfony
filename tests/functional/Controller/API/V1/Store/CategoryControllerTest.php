<?php

declare(strict_types=1);

namespace App\Tests\functional\Controller\API\V1\Store;

use App\Entity\Category;
use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use App\Tests\traits\ApiRequestTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/** @psalm-type categoryResponse array{id: int, isViewable: bool} */
class CategoryControllerTest extends WebTestCase
{
    use ApiRequestTrait;

    private const string STORE_URL = '/api/v1/store/';
    private const string LIST_URL = self::STORE_URL.'categories';
    private const string READ_URL = self::STORE_URL.'category/';

    private KernelBrowser $client;
    private CategoryRepository $categoryRepository;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->catchExceptions(false);

        $container = self::getContainer();
        $entityManager = $container->get('doctrine')->getManager();

        $this->categoryRepository = $entityManager->getRepository(Category::class);
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
     * @return list<array{string, list<categoryResponse>}>
     */
    public static function providerUsernameCategoryList(): array
    {
        $viewableCategories = [
            [
                'id' => 1,
                'isViewable' => true,
            ],
            [
                'id' => 14,
                'isViewable' => true,
            ],
            [
                'id' => 27,
                'isViewable' => true,
            ],
        ];
        $hiddenCategories = [
            [
                'id' => 40,
                'isViewable' => false,
            ],
            [
                'id' => 53,
                'isViewable' => false,
            ],
            [
                'id' => 66,
                'isViewable' => false,
            ],
        ];
        $allCategories = array_merge($viewableCategories, $hiddenCategories);

        return [
            ['temporary_user', $viewableCategories],
            ['valid_user', $viewableCategories],
            ['admin_user', $allCategories],
            ['super_admin_user', $allCategories],
        ];
    }

    /**
     * @dataProvider providerUsernameCategoryList
     *
     * @param list<categoryResponse> $categoryData
     */
    public function testListUnauthenticated(string $username, array $categoryData): void
    {
        $this->doCategoryListTest(null, $categoryData);
    }

    /**
     * @dataProvider providerUsernameCategoryList
     *
     * @param list<categoryResponse> $categoryData
     */
    public function testListAuthenticated(string $username, array $categoryData): void
    {
        $user = $this->userRepository->findOneBy(['usernameCanonical' => $username]);
        $this->assertNotNull($user);

        $this->doCategoryListTest($user, $categoryData);
    }

    private function assertIsCategoryData(array $data): void
    {
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('name', $data);
        $this->assertArrayHasKey('slug', $data);
        $this->assertArrayHasKey('description', $data);
        $this->assertArrayHasKey('isViewable', $data);
        $this->assertArrayHasKey('modifiedAt', $data);
    }

    /**
     * @param list<categoryResponse> $expectedCategoryData
     */
    private function doCategoryListTest(?User $user, array $expectedCategoryData): void
    {
        $this->makeApiRequest('GET', self::LIST_URL, null, null, $user);

        $this->assertResponseIsSuccessful();
        $response = $this->client->getResponse();
        $this->assertJson((string) $response->getContent());
        $jsonData = (array) json_decode((string) $response->getContent(), true);

        $this->assertArrayHasKey('category', $jsonData);
        $this->assertArrayHasKey('children', $jsonData);

        $this->assertNull($jsonData['category']);

        $this->assertIsArray($jsonData['children']);
        $this->assertArrayHasKey('categories', $jsonData['children']);
        $this->assertArrayHasKey('items', $jsonData['children']);

        $this->assertIsArray($jsonData['children']['categories']);
        /**
         * @var int              $index
         * @var categoryResponse $category
         */
        foreach ($jsonData['children']['categories'] as $index => $category) {
            $this->assertIsCategoryData($category);
            $this->assertSame($expectedCategoryData[$index]['id'], $category['id']);
            $this->assertSame($expectedCategoryData[$index]['isViewable'], $category['isViewable']);
        }

        $this->assertIsArray($jsonData['children']['items']);
        $this->assertEmpty($jsonData['children']['items']);
    }
}

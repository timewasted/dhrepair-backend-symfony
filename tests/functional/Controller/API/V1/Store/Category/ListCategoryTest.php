<?php

declare(strict_types=1);

namespace App\Tests\functional\Controller\API\V1\Store\Category;

use App\DTO\ReadCategoryResponse;
use App\Entity\Category;
use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use App\Tests\traits\ApiRequestTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ListCategoryTest extends WebTestCase
{
    use ApiRequestTrait;

    private const string LIST_URL = '/api/v1/store/categories';

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

    public function testListUnauthenticated(): void
    {
        /** @var Category[] $childCategories */
        $childCategories = [
            $this->categoryRepository->find(1),
            $this->categoryRepository->find(14),
            $this->categoryRepository->find(27),
        ];
        $dto = new ReadCategoryResponse(null, $childCategories, []);

        $this->doCategoryListTest(null, $dto);
    }

    /**
     * @dataProvider providerUsernameAndCanSeeHidden
     */
    public function testListAuthenticated(string $username, bool $canSeeHidden): void
    {
        $user = $this->userRepository->findOneBy(['usernameCanonical' => $username]);
        $childCategories = [
            $this->categoryRepository->find(1),
            $this->categoryRepository->find(14),
            $this->categoryRepository->find(27),
        ];
        if ($canSeeHidden) {
            $childCategories = array_merge($childCategories, [
                $this->categoryRepository->find(40),
                $this->categoryRepository->find(53),
                $this->categoryRepository->find(66),
            ]);
        }
        /** @var Category[] $childCategories */
        $dto = new ReadCategoryResponse(null, $childCategories, []);

        $this->doCategoryListTest($user, $dto);
    }

    private function doCategoryListTest(?User $user, ReadCategoryResponse $dto): void
    {
        $this->makeApiRequest('GET', self::LIST_URL, null, null, $user);

        $this->assertResponseIsSuccessful();
        $response = $this->client->getResponse();
        $this->assertJson((string) $response->getContent());
        $jsonData = (array) json_decode((string) $response->getContent(), true);

        $this->assertSame($dto->jsonSerialize(), $jsonData);
    }
}

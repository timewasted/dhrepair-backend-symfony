<?php

declare(strict_types=1);

namespace App\Tests\functional\Controller\API\V1\Store\Category;

use App\DTO\ReadCategoryResponse;
use App\DTO\UpdateCategoryRequest;
use App\Entity\Category;
use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use App\Tests\traits\ApiRequestTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class UpdateCategoryTest extends WebTestCase
{
    use ApiRequestTrait;

    private const string UPDATE_URL = '/api/v1/store/category';

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
    public static function providerUsernameUpdateStatus(): array
    {
        return [
            ['temporary_user', false],
            ['valid_user', false],
            ['admin_user', true],
            ['super_admin_user', true],
        ];
    }

    public function testUpdateUnauthenticated(): void
    {
        $this->expectException(AccessDeniedException::class);

        $id = 1;
        $parentId = null;
        $name = bin2hex(random_bytes(16));
        $description = bin2hex(random_bytes(16));
        $isViewable = false;
        $updateDto = new UpdateCategoryRequest($id, $parentId, $name, $description, $isViewable);
        $this->assertNotNull($this->categoryRepository->find($id));

        $this->makeApiRequest('PUT', self::UPDATE_URL, null, $updateDto);
    }

    public function testUpdateUnauthenticatedCategoryDoesNotExist(): void
    {
        $this->expectException(AccessDeniedException::class);

        $id = 999999;
        $parentId = null;
        $name = bin2hex(random_bytes(16));
        $description = bin2hex(random_bytes(16));
        $isViewable = false;
        $updateDto = new UpdateCategoryRequest($id, $parentId, $name, $description, $isViewable);
        $this->assertNull($this->categoryRepository->find($id));

        $this->makeApiRequest('PUT', self::UPDATE_URL, null, $updateDto);
    }

    public function testUpdateUnauthenticatedParentCategoryDoesNotExist(): void
    {
        $this->expectException(AccessDeniedException::class);

        $id = 1;
        $parentId = 999999;
        $name = bin2hex(random_bytes(16));
        $description = bin2hex(random_bytes(16));
        $isViewable = false;
        $updateDto = new UpdateCategoryRequest($id, $parentId, $name, $description, $isViewable);
        $this->assertNotNull($this->categoryRepository->find($id));
        $this->assertNull($this->categoryRepository->find($parentId));

        $this->makeApiRequest('PUT', self::UPDATE_URL, null, $updateDto);
    }

    /**
     * @dataProvider providerUsernameUpdateStatus
     */
    public function testUpdateAuthenticated(string $username, bool $expectAccessGranted): void
    {
        $user = $this->userRepository->findOneBy(['usernameCanonical' => $username]);
        if (!$expectAccessGranted) {
            $this->expectException(AccessDeniedException::class);
        }

        $id = 1;
        $parentId = null;
        $name = bin2hex(random_bytes(16));
        $description = bin2hex(random_bytes(16));
        $isViewable = false;
        $updateDto = new UpdateCategoryRequest($id, $parentId, $name, $description, $isViewable);
        $category = $this->categoryRepository->find($id);
        $this->assertNotNull($category);

        $this->makeApiRequest('PUT', self::UPDATE_URL, null, $updateDto, $user);

        if (!$expectAccessGranted) {
            return;
        }

        $this->assertResponseIsSuccessful();
        $response = $this->client->getResponse();
        $this->assertJson((string) $response->getContent());
        $jsonData = (array) json_decode((string) $response->getContent(), true);

        /** @var Category[] $children */
        $children = [
            $this->categoryRepository->find(10),
            $this->categoryRepository->find(2),
            $this->categoryRepository->find(6),
        ];
        $readDto = new ReadCategoryResponse($category, $children, []);
        $this->assertSame($readDto->jsonSerialize(), $jsonData);

        $this->assertNull($category->getParent());
        $this->assertSame($category->getName(), $updateDto->getName());
        $this->assertSame($category->getDescription(), $updateDto->getDescription());
        $this->assertSame($category->isViewable(), $updateDto->isViewable());
        $this->assertEqualsWithDelta((new \DateTimeImmutable())->getTimestamp(), (int) $category->getModifiedAt()?->getTimestamp(), 2);
    }

    /**
     * @dataProvider providerUsernameUpdateStatus
     */
    public function testUpdateAuthenticatedCategoryDoesNotExist(string $username, bool $expectAccessGranted): void
    {
        $user = $this->userRepository->findOneBy(['usernameCanonical' => $username]);
        if (!$expectAccessGranted) {
            $this->expectException(AccessDeniedException::class);
        }

        $id = 999999;
        $parentId = null;
        $name = bin2hex(random_bytes(16));
        $description = bin2hex(random_bytes(16));
        $isViewable = false;
        $updateDto = new UpdateCategoryRequest($id, $parentId, $name, $description, $isViewable);
        $this->assertNull($this->categoryRepository->find($id));

        $this->makeApiRequest('PUT', self::UPDATE_URL, null, $updateDto, $user);

        if ($expectAccessGranted) {
            $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @dataProvider providerUsernameUpdateStatus
     */
    public function testUpdateAuthenticatedParentCategoryDoesNotExist(string $username, bool $expectAccessGranted): void
    {
        $user = $this->userRepository->findOneBy(['usernameCanonical' => $username]);
        if (!$expectAccessGranted) {
            $this->expectException(AccessDeniedException::class);
        }

        $id = 1;
        $parentId = 999999;
        $name = bin2hex(random_bytes(16));
        $description = bin2hex(random_bytes(16));
        $isViewable = false;
        $updateDto = new UpdateCategoryRequest($id, $parentId, $name, $description, $isViewable);
        $category = $this->categoryRepository->find($id);
        $this->assertNotNull($category);
        $this->assertNull($this->categoryRepository->find($parentId));

        $this->makeApiRequest('PUT', self::UPDATE_URL, null, $updateDto, $user);

        if (!$expectAccessGranted) {
            return;
        }

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);

        $this->assertNull($category->getParent());
        $this->assertNotSame($category->getName(), $updateDto->getName());
        $this->assertNotSame($category->getDescription(), $updateDto->getDescription());
        $this->assertNotSame($category->isViewable(), $updateDto->isViewable());
    }
}

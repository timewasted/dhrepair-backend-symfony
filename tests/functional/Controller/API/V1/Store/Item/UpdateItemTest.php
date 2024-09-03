<?php

declare(strict_types=1);

namespace App\Tests\functional\Controller\API\V1\Store\Item;

use App\DTO\ReadItemResponse;
use App\DTO\UpdateItemRequest;
use App\Entity\Availability;
use App\Entity\Category;
use App\Entity\Image;
use App\Entity\Item;
use App\Entity\Manufacturer;
use App\Entity\User;
use App\Exception\DenormalizeEntity\EntityNotFoundException;
use App\Repository\AvailabilityRepository;
use App\Repository\CategoryRepository;
use App\Repository\ImageRepository;
use App\Repository\ItemRepository;
use App\Repository\ManufacturerRepository;
use App\Repository\UserRepository;
use App\Tests\traits\ApiRequestTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\String\Slugger\SluggerInterface;

class UpdateItemTest extends WebTestCase
{
    use ApiRequestTrait;

    private const string UPDATE_URL = '/api/v1/store/item';

    private KernelBrowser $client;
    private SluggerInterface $slugger;
    private AvailabilityRepository $availabilityRepository;
    private CategoryRepository $categoryRepository;
    private ImageRepository $imageRepository;
    private ItemRepository $itemRepository;
    private ManufacturerRepository $manufacturerRepository;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->catchExceptions(false);

        $container = self::getContainer();
        $this->slugger = $container->get(SluggerInterface::class);
        $entityManager = $container->get('doctrine')->getManager();

        $this->availabilityRepository = $entityManager->getRepository(Availability::class);
        $this->categoryRepository = $entityManager->getRepository(Category::class);
        $this->imageRepository = $entityManager->getRepository(Image::class);
        $this->itemRepository = $entityManager->getRepository(Item::class);
        $this->manufacturerRepository = $entityManager->getRepository(Manufacturer::class);
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
        $updateDto = $this->createUpdateItemRequest($id);
        $this->assertNotNull($this->itemRepository->find($id));

        $this->makeApiRequest('PUT', self::UPDATE_URL, null, $updateDto);
    }

    public function testUpdateUnauthenticatedItemDoesNotExist(): void
    {
        $this->expectException(AccessDeniedException::class);

        $id = 999999;
        $updateDto = $this->createUpdateItemRequest($id);
        $this->assertNull($this->itemRepository->find($id));

        $this->makeApiRequest('PUT', self::UPDATE_URL, null, $updateDto);
    }

    public function testUpdateUnauthenticatedManufacturerDoesNotExist(): void
    {
        $this->expectException(AccessDeniedException::class);

        $id = 1;
        $manufacturerId = 999999;
        $updateDto = $this->createUpdateItemRequest(itemId: $id, manufacturerId: $manufacturerId);
        $this->assertNotNull($this->itemRepository->find($id));
        $this->assertNull($this->manufacturerRepository->find($manufacturerId));

        $this->makeApiRequest('PUT', self::UPDATE_URL, null, $updateDto);
    }

    public function testUpdateUnauthenticatedAvailabilityDoesNotExist(): void
    {
        $this->expectException(AccessDeniedException::class);

        $id = 1;
        $availabilityId = 999999;
        $updateDto = $this->createUpdateItemRequest(itemId: $id, availabilityId: $availabilityId);
        $this->assertNotNull($this->itemRepository->find($id));
        $this->assertNull($this->availabilityRepository->find($availabilityId));

        $this->makeApiRequest('PUT', self::UPDATE_URL, null, $updateDto);
    }

    public function testUpdateUnauthenticatedCategoryDoesNotExist(): void
    {
        $this->expectException(AccessDeniedException::class);

        $id = 1;
        $categoryId = 999999;
        $updateDto = $this->createUpdateItemRequest(itemId: $id, categoryIds: [$categoryId]);
        $this->assertNotNull($this->itemRepository->find($id));
        $this->assertNull($this->categoryRepository->find($categoryId));

        $this->makeApiRequest('PUT', self::UPDATE_URL, null, $updateDto);
    }

    public function testUpdateUnauthenticatedImageDoesNotExist(): void
    {
        $this->expectException(AccessDeniedException::class);

        $id = 1;
        $imageId = 999999;
        $updateDto = $this->createUpdateItemRequest(itemId: $id, imageIds: [$imageId]);
        $this->assertNotNull($this->itemRepository->find($id));
        $this->assertNull($this->imageRepository->find($imageId));

        $this->makeApiRequest('PUT', self::UPDATE_URL, null, $updateDto);
    }

    /**
     * @dataProvider providerUsernameUpdateStatus
     */
    public function testUpdateAuthenticatedItemDoesNotExist(string $username, bool $expectAccessGranted): void
    {
        $user = $this->userRepository->findOneBy(['usernameCanonical' => $username]);
        if (!$expectAccessGranted) {
            $this->expectException(AccessDeniedException::class);
        }

        $id = 999999;
        $manufacturerId = 1;
        $availabilityId = 1;
        $categoryId = 1;
        $imageId = 1;
        $updateDto = $this->createUpdateItemRequest(
            itemId: $id,
            manufacturerId: $manufacturerId,
            availabilityId: $availabilityId,
            categoryIds: [$categoryId],
            imageIds: [$imageId],
        );
        $this->assertNull($this->itemRepository->find($id));
        $this->assertNotNull($this->manufacturerRepository->find($manufacturerId));
        $this->assertNotNull($this->availabilityRepository->find($availabilityId));
        $this->assertNotNull($this->categoryRepository->find($categoryId));
        $this->assertNotNull($this->imageRepository->find($imageId));

        $this->makeApiRequest('PUT', self::UPDATE_URL, null, $updateDto, $user);

        if (!$expectAccessGranted) {
            $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @dataProvider providerUsernameUpdateStatus
     */
    public function testUpdateAuthenticatedManufacturerDoesNotExist(string $username, bool $expectAccessGranted): void
    {
        $user = $this->userRepository->findOneBy(['usernameCanonical' => $username]);
        $id = 1;
        $manufacturerId = 999999;
        $availabilityId = 1;
        $categoryId = 1;
        $imageId = 1;
        if (!$expectAccessGranted) {
            $this->expectException(AccessDeniedException::class);
        } else {
            $this->expectException(EntityNotFoundException::class);
            $this->expectExceptionMessage(sprintf('Unable to find an instance of %s where "id" = "%s"', Manufacturer::class, $manufacturerId));
        }

        $updateDto = $this->createUpdateItemRequest(
            itemId: $id,
            manufacturerId: $manufacturerId,
            availabilityId: $availabilityId,
            categoryIds: [$categoryId],
            imageIds: [$imageId],
        );
        $this->assertNotNull($this->itemRepository->find($id));
        $this->assertNull($this->manufacturerRepository->find($manufacturerId));
        $this->assertNotNull($this->availabilityRepository->find($availabilityId));
        $this->assertNotNull($this->categoryRepository->find($categoryId));
        $this->assertNotNull($this->imageRepository->find($imageId));

        $this->makeApiRequest('PUT', self::UPDATE_URL, null, $updateDto, $user);
    }

    /**
     * @dataProvider providerUsernameUpdateStatus
     */
    public function testUpdateAuthenticatedAvailabilityDoesNotExist(string $username, bool $expectAccessGranted): void
    {
        $user = $this->userRepository->findOneBy(['usernameCanonical' => $username]);
        $id = 1;
        $manufacturerId = 1;
        $availabilityId = 999999;
        $categoryId = 1;
        $imageId = 1;
        if (!$expectAccessGranted) {
            $this->expectException(AccessDeniedException::class);
        } else {
            $this->expectException(EntityNotFoundException::class);
            $this->expectExceptionMessage(sprintf('Unable to find an instance of %s where "id" = "%s"', Availability::class, $availabilityId));
        }

        $updateDto = $this->createUpdateItemRequest(
            itemId: $id,
            manufacturerId: $manufacturerId,
            availabilityId: $availabilityId,
            categoryIds: [$categoryId],
            imageIds: [$imageId],
        );
        $this->assertNotNull($this->itemRepository->find($id));
        $this->assertNotNull($this->manufacturerRepository->find($manufacturerId));
        $this->assertNull($this->availabilityRepository->find($availabilityId));
        $this->assertNotNull($this->categoryRepository->find($categoryId));
        $this->assertNotNull($this->imageRepository->find($imageId));

        $this->makeApiRequest('PUT', self::UPDATE_URL, null, $updateDto, $user);
    }

    /**
     * @dataProvider providerUsernameUpdateStatus
     */
    public function testUpdateAuthenticatedCategoryDoesNotExist(string $username, bool $expectAccessGranted): void
    {
        $user = $this->userRepository->findOneBy(['usernameCanonical' => $username]);
        $id = 1;
        $manufacturerId = 1;
        $availabilityId = 1;
        $categoryId = 999999;
        $imageId = 1;
        if (!$expectAccessGranted) {
            $this->expectException(AccessDeniedException::class);
        } else {
            $this->expectException(EntityNotFoundException::class);
            $this->expectExceptionMessage(sprintf('Unable to find an instance of %s where "id" = "%s"', Category::class, $categoryId));
        }

        $updateDto = $this->createUpdateItemRequest(
            itemId: $id,
            manufacturerId: $manufacturerId,
            availabilityId: $availabilityId,
            categoryIds: [$categoryId],
            imageIds: [$imageId],
        );
        $this->assertNotNull($this->itemRepository->find($id));
        $this->assertNotNull($this->manufacturerRepository->find($manufacturerId));
        $this->assertNotNull($this->availabilityRepository->find($availabilityId));
        $this->assertNull($this->categoryRepository->find($categoryId));
        $this->assertNotNull($this->imageRepository->find($imageId));

        $this->makeApiRequest('PUT', self::UPDATE_URL, null, $updateDto, $user);
    }

    /**
     * @dataProvider providerUsernameUpdateStatus
     */
    public function testUpdateAuthenticatedImageDoesNotExist(string $username, bool $expectAccessGranted): void
    {
        $user = $this->userRepository->findOneBy(['usernameCanonical' => $username]);
        $id = 1;
        $manufacturerId = 1;
        $availabilityId = 1;
        $categoryId = 1;
        $imageId = 999999;
        if (!$expectAccessGranted) {
            $this->expectException(AccessDeniedException::class);
        } else {
            $this->expectException(EntityNotFoundException::class);
            $this->expectExceptionMessage(sprintf('Unable to find an instance of %s where "id" = "%s"', Image::class, $imageId));
        }

        $updateDto = $this->createUpdateItemRequest(
            itemId: $id,
            manufacturerId: $manufacturerId,
            availabilityId: $availabilityId,
            categoryIds: [$categoryId],
            imageIds: [$imageId],
        );
        $this->assertNotNull($this->itemRepository->find($id));
        $this->assertNotNull($this->manufacturerRepository->find($manufacturerId));
        $this->assertNotNull($this->availabilityRepository->find($availabilityId));
        $this->assertNotNull($this->categoryRepository->find($categoryId));
        $this->assertNull($this->imageRepository->find($imageId));

        $this->makeApiRequest('PUT', self::UPDATE_URL, null, $updateDto, $user);

        if (!$expectAccessGranted) {
            $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @dataProvider providerUsernameUpdateStatus
     */
    public function testUpdateAuthenticatedSinglePathToRootCategory(string $username, bool $expectAccessGranted): void
    {
        $user = $this->userRepository->findOneBy(['usernameCanonical' => $username]);
        if (!$expectAccessGranted) {
            $this->expectException(AccessDeniedException::class);
        }

        $id = 1;
        $manufacturerId = 1;
        $availabilityId = 1;
        $categoryId = 5;
        $imageId = 1;
        $updateDto = $this->createUpdateItemRequest(
            itemId: $id,
            manufacturerId: $manufacturerId,
            availabilityId: $availabilityId,
            categoryIds: [$categoryId],
            imageIds: [$imageId],
        );
        $this->assertNotNull($this->itemRepository->find($id));
        $this->assertNotNull($this->manufacturerRepository->find($manufacturerId));
        $this->assertNotNull($this->availabilityRepository->find($availabilityId));
        $this->assertNotNull($this->categoryRepository->find($categoryId));
        $this->assertNotNull($this->imageRepository->find($imageId));

        $this->makeApiRequest('PUT', self::UPDATE_URL, null, $updateDto, $user);

        if (!$expectAccessGranted) {
            return;
        }

        /** @var Item $item */
        $item = $this->itemRepository->find($id);
        /** @var list<list<Category>> $pathsToRootCategory */
        $pathsToRootCategory = [
            [
                $this->categoryRepository->find(5),
                $this->categoryRepository->find(2),
                $this->categoryRepository->find(1),
            ],
        ];
        $readDto = new ReadItemResponse($item, $pathsToRootCategory);
        $this->validateItemResponse($updateDto, $readDto);
    }

    /**
     * @dataProvider providerUsernameUpdateStatus
     */
    public function testUpdateAuthenticatedMultiplePathsToRootCategory(string $username, bool $expectAccessGranted): void
    {
        $user = $this->userRepository->findOneBy(['usernameCanonical' => $username]);
        if (!$expectAccessGranted) {
            $this->expectException(AccessDeniedException::class);
        }

        $id = 1;
        $manufacturerId = 1;
        $availabilityId = 1;
        $categoryIds = [5, 9];
        $imageId = 1;
        $updateDto = $this->createUpdateItemRequest(
            itemId: $id,
            manufacturerId: $manufacturerId,
            availabilityId: $availabilityId,
            categoryIds: $categoryIds,
            imageIds: [$imageId],
        );
        $this->assertNotNull($this->itemRepository->find($id));
        $this->assertNotNull($this->manufacturerRepository->find($manufacturerId));
        $this->assertNotNull($this->availabilityRepository->find($availabilityId));
        foreach ($categoryIds as $categoryId) {
            $this->assertNotNull($this->categoryRepository->find($categoryId));
        }
        $this->assertNotNull($this->imageRepository->find($imageId));

        $this->makeApiRequest('PUT', self::UPDATE_URL, null, $updateDto, $user);

        if (!$expectAccessGranted) {
            return;
        }

        /** @var Item $item */
        $item = $this->itemRepository->find($id);
        /** @var list<list<Category>> $pathsToRootCategory */
        $pathsToRootCategory = [
            [
                $this->categoryRepository->find(5),
                $this->categoryRepository->find(2),
                $this->categoryRepository->find(1),
            ],
            [
                $this->categoryRepository->find(9),
                $this->categoryRepository->find(6),
                $this->categoryRepository->find(1),
            ],
        ];
        $readDto = new ReadItemResponse($item, $pathsToRootCategory);
        $this->validateItemResponse($updateDto, $readDto);
    }

    /**
     * @dataProvider providerUsernameUpdateStatus
     */
    public function testUpdateAuthenticatedMultipleImages(string $username, bool $expectAccessGranted): void
    {
        $user = $this->userRepository->findOneBy(['usernameCanonical' => $username]);
        if (!$expectAccessGranted) {
            $this->expectException(AccessDeniedException::class);
        }

        $id = 1;
        $manufacturerId = 1;
        $availabilityId = 1;
        $categoryId = 5;
        $imageIds = [3, 1, 2, 4];
        $updateDto = $this->createUpdateItemRequest(
            itemId: $id,
            manufacturerId: $manufacturerId,
            availabilityId: $availabilityId,
            categoryIds: [$categoryId],
            imageIds: $imageIds,
        );
        $this->assertNotNull($this->itemRepository->find($id));
        $this->assertNotNull($this->manufacturerRepository->find($manufacturerId));
        $this->assertNotNull($this->availabilityRepository->find($availabilityId));
        $this->assertNotNull($this->categoryRepository->find($categoryId));
        foreach ($imageIds as $imageId) {
            $this->assertNotNull($this->imageRepository->find($imageId));
        }

        $this->makeApiRequest('PUT', self::UPDATE_URL, null, $updateDto, $user);

        if (!$expectAccessGranted) {
            return;
        }

        /** @var Item $item */
        $item = $this->itemRepository->find($id);
        /** @var list<list<Category>> $pathsToRootCategory */
        $pathsToRootCategory = [
            [
                $this->categoryRepository->find(5),
                $this->categoryRepository->find(2),
                $this->categoryRepository->find(1),
            ],
        ];
        $readDto = new ReadItemResponse($item, $pathsToRootCategory);
        $this->validateItemResponse($updateDto, $readDto);
    }

    private function createUpdateItemRequest(
        ?int $itemId = null,
        ?int $manufacturerId = null,
        ?int $availabilityId = null,
        ?array $categoryIds = null,
        ?array $imageIds = null,
    ): UpdateItemRequest {
        return new UpdateItemRequest(
            $itemId ?? random_int(1, PHP_INT_MAX),
            'this is a RANDOM name '.bin2hex(random_bytes(8)),
            'this is a RANDOM sku '.bin2hex(random_bytes(8)),
            bin2hex(random_bytes(16)),
            $manufacturerId ?? random_int(1, PHP_INT_MAX),
            random_int(1, 9_999_999),
            random_int(1, 9_999_999),
            $availabilityId ?? random_int(1, PHP_INT_MAX),
            sprintf('%d.%02d', random_int(0, 999), random_int(0, 99)),
            sprintf('%d.%02d', random_int(0, 999), random_int(0, 99)),
            sprintf('%d.%02d', random_int(0, 999), random_int(0, 99)),
            sprintf('%d.%02d', random_int(0, 999), random_int(0, 99)),
            (bool) random_int(0, 1),
            (bool) random_int(0, 1),
            (bool) random_int(0, 1),
            (bool) random_int(0, 1),
            (bool) random_int(0, 1),
            (bool) random_int(0, 1),
            (bool) random_int(0, 1),
            (bool) random_int(0, 1),
            (bool) random_int(0, 1),
            $categoryIds ?? [random_int(1, PHP_INT_MAX), random_int(1, PHP_INT_MAX)],
            $imageIds ?? [random_int(1, PHP_INT_MAX), random_int(1, PHP_INT_MAX)],
        );
    }

    private function validateItemResponse(UpdateItemRequest $updateDto, ReadItemResponse $readDto): void
    {
        $this->assertResponseIsSuccessful();
        $response = $this->client->getResponse();
        $this->assertJson((string) $response->getContent());
        $jsonData = (array) json_decode((string) $response->getContent(), true);

        $this->assertSame($readDto->jsonSerialize(), $jsonData);

        /** @var Item $item */
        $item = $this->itemRepository->find($updateDto->getId());
        $this->assertSame($updateDto->getName(), $item->getName());
        $this->assertSame((string) $this->slugger->slug($updateDto->getSku().' '.$updateDto->getName())->lower(), $item->getSlug());
        $this->assertSame($updateDto->getSku(), $item->getSku());
        $this->assertSame($updateDto->getDescription(), $item->getDescription());
        $this->assertSame($updateDto->getManufacturerId(), $item->getManufacturer()?->getId());
        $this->assertSame($updateDto->getCost(), $item->getCost());
        $this->assertSame($updateDto->getQuantity(), $item->getQuantity());
        $this->assertSame($updateDto->getAvailabilityId(), $item->getAvailability()?->getId());
        $this->assertSame($updateDto->getWeight(), $item->getWeight());
        $this->assertSame($updateDto->getLength(), $item->getLength());
        $this->assertSame($updateDto->getWidth(), $item->getWidth());
        $this->assertSame($updateDto->getHeight(), $item->getHeight());
        $this->assertSame($updateDto->isProduct(), $item->isProduct());
        $this->assertSame($updateDto->isViewable(), $item->isViewable());
        $this->assertSame($updateDto->isPurchasable(), $item->isPurchasable());
        $this->assertSame($updateDto->isSpecial(), $item->isSpecial());
        $this->assertSame($updateDto->isNew(), $item->isNew());
        $this->assertSame($updateDto->isChargeTax(), $item->isChargeTax());
        $this->assertSame($updateDto->isChargeShipping(), $item->isChargeShipping());
        $this->assertSame($updateDto->isFreeShipping(), $item->isFreeShipping());
        $this->assertSame($updateDto->isFreightQuoteRequired(), $item->isFreightQuoteRequired());
        $this->assertEqualsWithDelta((new \DateTimeImmutable())->getTimestamp(), (int) $item->getModifiedAt()?->getTimestamp(), 2);

        $this->assertCount(count($updateDto->getCategoryIds()), $item->getCategories());
        /**
         * @var int $index
         * @var int $categoryId
         */
        foreach ($updateDto->getCategoryIds() as $index => $categoryId) {
            $this->assertSame($categoryId, $item->getCategories()[$index]->getId());
        }

        $this->assertCount(count($updateDto->getImageIds()), $item->getImages());
        /** @var int $imageId */
        foreach ($updateDto->getImageIds() as $index => $imageId) {
            $this->assertSame($imageId, $item->getImages()[$index]->getId());
        }
    }
}

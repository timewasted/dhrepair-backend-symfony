<?php

declare(strict_types=1);

namespace App\Tests\functional\Controller\API\V1\Store\Image;

use App\DTO\ReadImageResponse;
use App\Entity\Image;
use App\Entity\User;
use App\Repository\ImageRepository;
use App\Repository\UserRepository;
use App\Tests\traits\ApiRequestTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ReadImageTest extends WebTestCase
{
    use ApiRequestTrait;

    private const string READ_URL = '/api/v1/store/image/';

    private KernelBrowser $client;
    private ImageRepository $imageRepository;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->catchExceptions(false);

        $container = self::getContainer();
        $entityManager = $container->get('doctrine')->getManager();

        $this->imageRepository = $entityManager->getRepository(Image::class);
        $this->userRepository = $entityManager->getRepository(User::class);
    }

    /**
     * @return list<array{string, bool}>
     */
    public static function providerUsernameAccessGranted(): array
    {
        return [
            ['temporary_user', false],
            ['valid_user', false],
            ['admin_user', true],
            ['super_admin_user', true],
        ];
    }

    public function testReadUnauthenticatedImageDoesNotExist(): void
    {
        $this->expectException(AccessDeniedException::class);

        $imageId = 999999;
        $this->assertNull($this->imageRepository->find($imageId));

        $this->makeApiRequest('GET', self::READ_URL.$imageId);
    }

    public function testReadUnauthenticated(): void
    {
        $this->expectException(AccessDeniedException::class);

        $imageId = 1;
        $this->assertNotNull($this->imageRepository->find($imageId));

        $this->makeApiRequest('GET', self::READ_URL.$imageId);
    }

    /**
     * @dataProvider providerUsernameAccessGranted
     */
    public function testReadAuthenticatedImageDoesNotExist(string $username, bool $expectAccessGranted): void
    {
        $user = $this->userRepository->findOneBy(['usernameCanonical' => $username]);
        if (!$expectAccessGranted) {
            $this->expectException(AccessDeniedException::class);
        }

        $imageId = 999999;
        $this->assertNull($this->imageRepository->find($imageId));

        $this->makeApiRequest('GET', self::READ_URL.$imageId, null, null, $user);

        if ($expectAccessGranted) {
            $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @dataProvider providerUsernameAccessGranted
     */
    public function testReadAuthenticated(string $username, bool $expectAccessGranted): void
    {
        $user = $this->userRepository->findOneBy(['usernameCanonical' => $username]);
        if (!$expectAccessGranted) {
            $this->expectException(AccessDeniedException::class);
        }

        $imageId = 1;
        $image = $this->imageRepository->find($imageId);
        $this->assertNotNull($image);

        $this->makeApiRequest('GET', self::READ_URL.$imageId, null, null, $user);

        if (!$expectAccessGranted) {
            return;
        }

        $this->assertResponseIsSuccessful();
        $response = $this->client->getResponse();
        $this->assertJson((string) $response->getContent());
        $jsonData = (array) json_decode((string) $response->getContent(), true);

        $dto = new ReadImageResponse($image);
        $this->assertSame($dto->jsonSerialize(), $jsonData);
    }
}

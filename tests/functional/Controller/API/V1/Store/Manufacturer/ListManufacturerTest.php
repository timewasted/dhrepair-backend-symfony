<?php

declare(strict_types=1);

namespace App\Tests\functional\Controller\API\V1\Store\Manufacturer;

use App\DTO\ReadManufacturerResponse;
use App\Entity\Manufacturer;
use App\Entity\User;
use App\Repository\ManufacturerRepository;
use App\Repository\UserRepository;
use App\Tests\traits\ApiRequestTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ListManufacturerTest extends WebTestCase
{
    use ApiRequestTrait;

    private const string LIST_URL = '/api/v1/store/manufacturers';

    private KernelBrowser $client;
    private ManufacturerRepository $manufacturerRepository;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->catchExceptions(false);

        $container = self::getContainer();
        $entityManager = $container->get('doctrine')->getManager();

        $this->manufacturerRepository = $entityManager->getRepository(Manufacturer::class);
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

    public function testListUnauthenticated(): void
    {
        $this->expectException(AccessDeniedException::class);
        $this->makeApiRequest('GET', self::LIST_URL);
    }

    /**
     * @dataProvider providerUsernameAccessGranted
     */
    public function testListAuthenticated(string $username, bool $expectAccessGranted): void
    {
        $user = $this->userRepository->findOneBy(['usernameCanonical' => $username]);
        if (!$expectAccessGranted) {
            $this->expectException(AccessDeniedException::class);
        }

        $this->makeApiRequest('GET', self::LIST_URL, null, null, $user);

        if (!$expectAccessGranted) {
            return;
        }

        $this->assertResponseIsSuccessful();
        $response = $this->client->getResponse();
        $this->assertJson((string) $response->getContent());
        $jsonData = (array) json_decode((string) $response->getContent(), true);

        $dto = new ReadManufacturerResponse($this->manufacturerRepository->findBy([], ['name' => 'ASC']));
        $this->assertSame($dto->jsonSerialize(), $jsonData);
    }
}

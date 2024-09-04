<?php

declare(strict_types=1);

namespace App\Tests\functional\Controller\API\V1\Store\Cart;

use App\DTO\ReadCartResponse;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Tests\traits\ApiRequestTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class DeleteCartTest extends WebTestCase
{
    use ApiRequestTrait;

    private const string DELETE_URL = '/api/v1/store/cart';

    private KernelBrowser $client;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->catchExceptions(false);

        $container = self::getContainer();
        $entityManager = $container->get('doctrine')->getManager();

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

    public function testDeleteUnauthenticated(): void
    {
        $this->expectException(AccessDeniedException::class);
        $this->makeApiRequest('DELETE', self::DELETE_URL);
    }

    /**
     * @dataProvider providerUsername
     */
    public function testDeleteAuthenticated(string $username): void
    {
        $user = $this->userRepository->findOneBy(['usernameCanonical' => $username]);
        $this->assertNotNull($user);

        $this->makeApiRequest('DELETE', self::DELETE_URL, null, null, $user);

        $this->assertResponseIsSuccessful();
        $response = $this->client->getResponse();
        $this->assertJson((string) $response->getContent());
        $jsonData = (array) json_decode((string) $response->getContent(), true);

        $this->assertSame((new ReadCartResponse([]))->jsonSerialize(), $jsonData);
    }
}

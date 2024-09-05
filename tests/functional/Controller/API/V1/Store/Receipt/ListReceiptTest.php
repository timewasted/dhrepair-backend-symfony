<?php

declare(strict_types=1);

namespace App\Tests\functional\Controller\API\V1\Store\Receipt;

use App\DTO\ListOrderResponse;
use App\Entity\Order;
use App\Entity\User;
use App\Repository\OrderRepository;
use App\Repository\UserRepository;
use App\Tests\traits\ApiRequestTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ListReceiptTest extends WebTestCase
{
    use ApiRequestTrait;

    private const string LIST_URL = '/api/v1/store/receipts';

    private KernelBrowser $client;
    private OrderRepository $orderRepository;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->catchExceptions(false);

        $container = self::getContainer();
        $entityManager = $container->get('doctrine')->getManager();

        $this->orderRepository = $entityManager->getRepository(Order::class);
        $this->userRepository = $entityManager->getRepository(User::class);
    }

    /**
     * @return list<array{string, bool}>
     */
    public static function providerUsernameAndCanGetAllOrders(): array
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
     * @dataProvider providerUsernameAndCanGetAllOrders
     */
    public function testListAuthenticated(string $username, bool $canGetAllReceipts): void
    {
        /** @var User $user */
        $user = $this->userRepository->findOneBy(['usernameCanonical' => $username]);
        if ($canGetAllReceipts) {
            /** @var Order[] $orders */
            $orders = $this->orderRepository->findAll();
        } else {
            /** @var Order[] $orders */
            $orders = $this->orderRepository->findBy(['username' => $username]);
        }
        $dto = new ListOrderResponse($orders);

        $this->makeApiRequest('GET', self::LIST_URL, null, null, $user);

        $this->assertResponseIsSuccessful();
        $response = $this->client->getResponse();
        $this->assertJson((string) $response->getContent());
        $jsonData = (array) json_decode((string) $response->getContent(), true);

        $this->assertSame($dto->jsonSerialize(), $jsonData);
    }
}

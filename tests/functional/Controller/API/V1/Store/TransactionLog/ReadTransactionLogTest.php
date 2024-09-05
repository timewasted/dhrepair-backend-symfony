<?php

declare(strict_types=1);

namespace App\Tests\functional\Controller\API\V1\Store\TransactionLog;

use App\DTO\ReadTransactionLogResponse;
use App\Entity\TransactionLog;
use App\Entity\User;
use App\Repository\TransactionLogRepository;
use App\Repository\UserRepository;
use App\Tests\traits\ApiRequestTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ReadTransactionLogTest extends WebTestCase
{
    use ApiRequestTrait;

    private const string READ_URL = '/api/v1/store/transaction/';

    private KernelBrowser $client;
    private TransactionLogRepository $transactionLogRepository;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->catchExceptions(false);

        $container = self::getContainer();
        $entityManager = $container->get('doctrine')->getManager();

        $this->transactionLogRepository = $entityManager->getRepository(TransactionLog::class);
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

    public function testReadUnauthenticated(): void
    {
        $this->expectException(AccessDeniedException::class);
        $orderId = 1;
        $this->makeApiRequest('GET', self::READ_URL.$orderId);
    }

    /**
     * @dataProvider providerUsernameAccessGranted
     */
    public function testReadAuthenticatedOrderIdDoesNotExist(string $username, bool $expectAccessGranted): void
    {
        /** @var User $user */
        $user = $this->userRepository->findOneBy(['usernameCanonical' => $username]);
        if (!$expectAccessGranted) {
            $this->expectException(AccessDeniedException::class);
        }
        $orderId = 9999;

        $this->makeApiRequest('GET', self::READ_URL.$orderId, null, null, $user);

        if (!$expectAccessGranted) {
            return;
        }

        $jsonData = $this->getJsonData(Response::HTTP_NOT_FOUND);
        $this->assertArrayHasKey('orderId', $jsonData);
        $this->assertSame((string) $orderId, $jsonData['orderId']);
    }

    /**
     * @dataProvider providerUsernameAccessGranted
     */
    public function testReadAuthenticated(string $username, bool $expectAccessGranted): void
    {
        /** @var User $user */
        $user = $this->userRepository->findOneBy(['usernameCanonical' => $username]);
        if (!$expectAccessGranted) {
            $this->expectException(AccessDeniedException::class);
        }
        $orderId = 1;

        $this->makeApiRequest('GET', self::READ_URL.$orderId, null, null, $user);

        if ($expectAccessGranted) {
            /** @var TransactionLog[] $transactions */
            $transactions = [
                $this->transactionLogRepository->find(1),
                $this->transactionLogRepository->find(2),
                $this->transactionLogRepository->find(3),
            ];
            $dto = new ReadTransactionLogResponse($transactions);

            $this->assertSame($dto->jsonSerialize(), $this->getJsonData());
        }
    }

    private function getJsonData(int $expectedResponse = Response::HTTP_OK): array
    {
        $this->assertResponseStatusCodeSame($expectedResponse);
        $response = $this->client->getResponse();
        $this->assertJson((string) $response->getContent());

        return (array) json_decode((string) $response->getContent(), true);
    }
}

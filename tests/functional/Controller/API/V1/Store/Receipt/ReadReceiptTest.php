<?php

declare(strict_types=1);

namespace App\Tests\functional\Controller\API\V1\Store\Receipt;

use App\DTO\ReadOrderResponse;
use App\Entity\Order;
use App\Repository\OrderRepository;
use App\Tests\traits\ApiRequestTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ReadReceiptTest extends WebTestCase
{
    use ApiRequestTrait;

    private const string READ_URL = '/api/v1/store/receipt/';

    private KernelBrowser $client;
    private OrderRepository $orderRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->catchExceptions(false);

        $container = self::getContainer();
        $entityManager = $container->get('doctrine')->getManager();

        $this->orderRepository = $entityManager->getRepository(Order::class);
    }

    public function testReadLegacyOrderIdDoesNotExist(): void
    {
        $orderId = '9999';
        $receiptId = 'cd81933b1dd7a3b7280bc09da4ff478751355c99';

        $this->makeApiRequest('GET', self::READ_URL.$orderId.'/'.$receiptId);
        $this->validateLegacyReceiptNotFoundResponse($orderId, $receiptId);
    }

    public function testReadLegacyReceiptIdDoesNotExist(): void
    {
        $orderId = '0005';
        $receiptId = '0000000000000000000000000000000000000000';

        $this->makeApiRequest('GET', self::READ_URL.$orderId.'/'.$receiptId);
        $this->validateLegacyReceiptNotFoundResponse($orderId, $receiptId);
    }

    public function testReadLegacy(): void
    {
        $orderId = '0005';
        $receiptId = 'cd81933b1dd7a3b7280bc09da4ff478751355c99';
        $order = $this->orderRepository->findOneBy(['id' => $orderId, 'receiptId' => $receiptId]);
        $this->assertNotNull($order);
        $dto = new ReadOrderResponse($order);

        $this->makeApiRequest('GET', self::READ_URL.$orderId.'/'.$receiptId);
        $this->validateReceiptResponse($dto);
    }

    public function testReadCurrentOrderNumberDoesNotExist(): void
    {
        $orderNumber = '99-090124-111';
        $receiptId = '59b9759a01fa8eea124be6fc47faa7b13f9f77b3';

        $this->makeApiRequest('GET', self::READ_URL.$orderNumber.'/'.$receiptId);
        $this->validateCurrentReceiptNotFoundResponse($orderNumber, $receiptId);
    }

    public function testReadCurrentReceiptIdDoesNotExist(): void
    {
        $orderNumber = '01-090124-111';
        $receiptId = '0000000000000000000000000000000000000000';

        $this->makeApiRequest('GET', self::READ_URL.$orderNumber.'/'.$receiptId);
        $this->validateCurrentReceiptNotFoundResponse($orderNumber, $receiptId);
    }

    public function testReadCurrent(): void
    {
        $orderNumber = '01-090124-111';
        $receiptId = '59b9759a01fa8eea124be6fc47faa7b13f9f77b3';
        $order = $this->orderRepository->findOneBy(['orderNumber' => $orderNumber, 'receiptId' => $receiptId]);
        $this->assertNotNull($order);
        $dto = new ReadOrderResponse($order);

        $this->makeApiRequest('GET', self::READ_URL.$orderNumber.'/'.$receiptId);
        $this->validateReceiptResponse($dto);
    }

    private function getJsonData(int $expectedResponse = Response::HTTP_OK): array
    {
        $this->assertResponseStatusCodeSame($expectedResponse);
        $response = $this->client->getResponse();
        $this->assertJson((string) $response->getContent());

        return (array) json_decode((string) $response->getContent(), true);
    }

    private function validateCurrentReceiptNotFoundResponse(string $orderNumber, string $receiptId): void
    {
        $jsonData = $this->getJsonData(Response::HTTP_NOT_FOUND);

        $this->assertArrayHasKey('orderNumber', $jsonData);
        $this->assertSame($orderNumber, $jsonData['orderNumber']);
        $this->assertArrayHasKey('receiptId', $jsonData);
        $this->assertSame($receiptId, $jsonData['receiptId']);
    }

    private function validateLegacyReceiptNotFoundResponse(string $orderId, string $receiptId): void
    {
        $jsonData = $this->getJsonData(Response::HTTP_NOT_FOUND);

        $this->assertArrayHasKey('orderId', $jsonData);
        $this->assertSame($orderId, $jsonData['orderId']);
        $this->assertArrayHasKey('receiptId', $jsonData);
        $this->assertSame($receiptId, $jsonData['receiptId']);
    }

    private function validateReceiptResponse(ReadOrderResponse $dto): void
    {
        $jsonData = $this->getJsonData();

        $this->assertSame($dto->jsonSerialize(), $jsonData);
    }
}

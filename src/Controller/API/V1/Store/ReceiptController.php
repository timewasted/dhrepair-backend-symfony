<?php

declare(strict_types=1);

namespace App\Controller\API\V1\Store;

use App\DTO\ReadOrderResponse;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/store', name: 'store_receipt_')]
class ReceiptController extends AbstractController
{
    #[Route(
        '/receipt/{orderNumber}/{receiptId}',
        name: 'read',
        requirements: [
            'orderNumber' => '^\d+-\d{6}-\d{3,}$',
            'receiptId' => '^[A-Za-z0-9]{40}$',
        ],
        methods: ['GET']
    )]
    public function read(
        string $orderNumber,
        string $receiptId,
        OrderRepository $repository
    ): Response {
        $order = $repository->findOneBy(['orderNumber' => $orderNumber, 'receiptId' => $receiptId]);
        if (null === $order) {
            return $this->json([
                'orderNumber' => $orderNumber,
                'receiptId' => $receiptId,
            ], Response::HTTP_NOT_FOUND);
        }

        return $this->json(new ReadOrderResponse($order));
    }

    #[Route(
        '/receipt/{orderId}/{receiptId}',
        name: 'readLegacy',
        requirements: [
            'orderId' => '^\d{4}$',
            'receiptId' => '^[A-Za-z0-9]{40}$',
        ],
        methods: ['GET']
    )]
    public function readLegacy(
        string $orderId,
        string $receiptId,
        OrderRepository $repository
    ): Response {
        $order = $repository->findOneBy(['id' => $orderId, 'receiptId' => $receiptId]);
        if (null === $order) {
            return $this->json([
                'orderId' => $orderId,
                'receiptId' => $receiptId,
            ], Response::HTTP_NOT_FOUND);
        }

        return $this->json(new ReadOrderResponse($order));
    }
}

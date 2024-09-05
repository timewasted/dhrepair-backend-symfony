<?php

declare(strict_types=1);

namespace App\Controller\API\V1\Store;

use App\DTO\ReadTransactionLogResponse;
use App\Entity\User;
use App\Repository\TransactionLogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/store', name: 'store_transaction_log_')]
class TransactionLogController extends AbstractController
{
    #[IsGranted(User::ROLE_ADMIN)]
    #[Route('/transaction/{orderId}', name: 'read', requirements: ['orderId' => '^\d+$'], methods: ['GET'])]
    public function read(
        string $orderId,
        TransactionLogRepository $repository,
    ): Response {
        $transactions = $repository->findBy(['orderInfo' => $orderId]);
        if (empty($transactions)) {
            return $this->json([
                'orderId' => $orderId,
            ], Response::HTTP_NOT_FOUND);
        }

        return $this->json(new ReadTransactionLogResponse($transactions));
    }
}

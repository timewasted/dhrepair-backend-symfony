<?php

declare(strict_types=1);

namespace App\DTO;

use App\Entity\TransactionLog;

readonly class ReadTransactionLogResponse implements \JsonSerializable
{
    private array $jsonData;

    /**
     * @param TransactionLog[] $transactionLogs
     */
    public function __construct(array $transactionLogs)
    {
        $jsonData = [];
        foreach ($transactionLogs as $transactionLog) {
            $jsonData[] = [
                'id' => $transactionLog->getId(),
                'orderId' => $transactionLog->getOrderInfo()?->getId(),
                'referencedId' => $transactionLog->getReferencedId(),
                'transactionId' => $transactionLog->getTransactionId(),
                'action' => $transactionLog->getAction(),
                'amount' => $transactionLog->getAmount(),
                'isSuccess' => $transactionLog->isSuccess(),
                'isAvsSuccess' => $transactionLog->isAvsSuccess(),
                'isCvv2Success' => $transactionLog->isCvv2Success(),
                'createdAt' => $transactionLog->getCreatedAt()?->format(\DateTimeInterface::ATOM),
            ];
        }
        $this->jsonData = $jsonData;
    }

    public function jsonSerialize(): array
    {
        return $this->jsonData;
    }
}

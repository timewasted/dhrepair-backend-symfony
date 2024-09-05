<?php

declare(strict_types=1);

namespace App\Tests\unit\DTO;

use App\Entity\TransactionLog;

trait TransactionLogTestTrait
{
    protected function createTransactionLog(): TransactionLog
    {
        return (new TransactionLog())
            ->setReferencedId(bin2hex(random_bytes(16)))
            ->setTransactionId(bin2hex(random_bytes(16)))
            ->setAction(bin2hex(random_bytes(16)))
            ->setAmount(random_int(0, PHP_INT_MAX))
            ->setIsSuccess((bool) random_int(0, 1))
            ->setIsAvsSuccess((bool) random_int(0, 1))
            ->setIsCvv2Success((bool) random_int(0, 1))
        ;
    }

    protected function getTransactionLogData(TransactionLog $transactionLog): array
    {
        return [
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
}

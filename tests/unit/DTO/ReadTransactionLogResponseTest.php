<?php

declare(strict_types=1);

namespace App\Tests\unit\DTO;

use App\DTO\ReadTransactionLogResponse;
use PHPUnit\Framework\TestCase;

class ReadTransactionLogResponseTest extends TestCase
{
    use TransactionLogTestTrait;

    public function testJsonSerialize(): void
    {
        $transactionLog1 = $this->createTransactionLog();
        $transactionLog2 = $this->createTransactionLog();
        $dto = new ReadTransactionLogResponse([$transactionLog1, $transactionLog2]);

        $this->assertSame([
            $this->getTransactionLogData($transactionLog1),
            $this->getTransactionLogData($transactionLog2),
        ], $dto->jsonSerialize());
    }
}

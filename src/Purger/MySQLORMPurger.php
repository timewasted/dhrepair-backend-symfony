<?php

declare(strict_types=1);

namespace App\Purger;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;

class MySQLORMPurger extends ORMPurger
{
    private bool $disableForeignKeyChecks = false;

    public function setDisableForeignKeyChecks(bool $disableForeignKeyChecks = true): void
    {
        $this->disableForeignKeyChecks = $disableForeignKeyChecks;
    }

    public function purge(): void
    {
        $connection = $this->getObjectManager()->getConnection();
        $pdo = $connection->getNativeConnection();
        if (!($pdo instanceof \PDO)) {
            throw new \RuntimeException(sprintf('Unsupported native connection "%s"', $pdo::class));
        }
        $isInTransaction = $pdo->inTransaction();

        try {
            if ($this->disableForeignKeyChecks) {
                $connection->executeStatement('SET FOREIGN_KEY_CHECKS = 0');
            }
            parent::purge();
        } finally {
            if ($this->disableForeignKeyChecks) {
                $connection->executeStatement('SET FOREIGN_KEY_CHECKS = 1');
            }
        }

        if ($isInTransaction && !$pdo->inTransaction()) {
            $pdo->beginTransaction();
        }
    }
}

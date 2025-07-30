<?php

declare(strict_types=1);

namespace App\Purger;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Purger\ORMPurgerInterface;
use Doctrine\ORM\EntityManagerInterface;

final class MySQLORMPurger implements ORMPurgerInterface
{
    private bool $disableForeignKeyChecks = false;

    public function __construct(private readonly ORMPurger $purger)
    {
    }

    public function setDisableForeignKeyChecks(bool $disableForeignKeyChecks = true): void
    {
        $this->disableForeignKeyChecks = $disableForeignKeyChecks;
    }

    #[\Override]
    public function setEntityManager(EntityManagerInterface $em): void
    {
        $this->purger->setEntityManager($em);
    }

    public function setPurgeMode(int $mode): void
    {
        $this->purger->setPurgeMode($mode);
    }

    #[\Override]
    public function purge(): void
    {
        $connection = $this->purger->getObjectManager()->getConnection();
        $pdo = $connection->getNativeConnection();
        if (!($pdo instanceof \PDO)) {
            throw new \RuntimeException(sprintf('Unsupported native connection "%s"', $pdo::class));
        }
        $isInTransaction = $pdo->inTransaction();

        try {
            if ($this->disableForeignKeyChecks) {
                $connection->executeStatement('SET FOREIGN_KEY_CHECKS = 0');
            }
            $this->purger->purge();
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

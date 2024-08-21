<?php

declare(strict_types=1);

namespace App\Purger;

use Doctrine\Bundle\FixturesBundle\Purger\PurgerFactory;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Purger\PurgerInterface;
use Doctrine\ORM\EntityManagerInterface;

readonly class MySQLORMPurgerFactory implements PurgerFactory
{
    public function __construct(private bool $disableForeignKeyChecks = false)
    {
    }

    public function createForEntityManager(
        ?string $emName,
        EntityManagerInterface $em,
        array $excluded = [],
        bool $purgeWithTruncate = false
    ): PurgerInterface {
        $purger = new MySQLORMPurger();
        $purger->setPurgeMode($purgeWithTruncate ? ORMPurger::PURGE_MODE_TRUNCATE : ORMPurger::PURGE_MODE_DELETE);
        $purger->setDisableForeignKeyChecks($this->disableForeignKeyChecks);

        return $purger;
    }
}

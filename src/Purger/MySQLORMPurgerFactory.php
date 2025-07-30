<?php

declare(strict_types=1);

namespace App\Purger;

use Doctrine\Bundle\FixturesBundle\Purger\PurgerFactory;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Purger\PurgerInterface;
use Doctrine\ORM\EntityManagerInterface;

/** @template-implements PurgerFactory<PurgerInterface> */
final readonly class MySQLORMPurgerFactory implements PurgerFactory
{
    public function __construct(private bool $disableForeignKeyChecks = false)
    {
    }

    #[\Override]
    public function createForEntityManager(
        ?string $emName,
        EntityManagerInterface $em,
        array $excluded = [],
        bool $purgeWithTruncate = false,
    ): PurgerInterface {
        $purger = new MySQLORMPurger(new ORMPurger());
        $purger->setPurgeMode($purgeWithTruncate ? ORMPurger::PURGE_MODE_TRUNCATE : ORMPurger::PURGE_MODE_DELETE);
        $purger->setDisableForeignKeyChecks($this->disableForeignKeyChecks);

        return $purger;
    }
}

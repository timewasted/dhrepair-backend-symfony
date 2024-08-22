<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\CategoryClosureRepository;
use Doctrine\DBAL\Exception as DBALException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:rebuild-closure-table',
    description: 'Rebuild the category_closure table',
)]
class RebuildClosureTableCommand extends Command
{
    public function __construct(
        private CategoryClosureRepository $repository,
        private EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->writeln('Rebuilding the category_closure table. This may take a moment...');

        try {
            $this->startRebuild();
        } catch (DBALException $e) {
            $io->error($e->getMessage());
            throw $e;
        }

        $io->writeln('Rebuild complete');

        return Command::SUCCESS;
    }

    /**
     * @throws DBALException
     */
    private function startRebuild(): void
    {
        $this->entityManager->getConnection()->executeStatement('TRUNCATE TABLE category_closure');
        $this->processCategory(null);
    }

    /**
     * @throws DBALException
     */
    private function processCategory(?int $parentId): void
    {
        foreach ($this->getChildren($parentId) as $category) {
            $this->repository->onCategoryParentChanged($category['id'], (int) $parentId, true);
            $this->processCategory($category['id']);
        }
    }

    /**
     * @return list<array{id: int, parent: ?int}>
     */
    private function getChildren(?int $parentId): array
    {
        $rsm = (new ResultSetMapping())
            ->addScalarResult('id', 'id')
            ->addScalarResult('parent', 'parent')
        ;

        if (null === $parentId) {
            /** @var list<array{id: int, parent: ?int}> $categories */
            $categories = $this->entityManager
                ->createNativeQuery('SELECT id, parent FROM category WHERE parent IS NULL ORDER BY id ASC', $rsm)
                ->execute()
            ;
        } else {
            /** @var list<array{id: int, parent: ?int}> $categories */
            $categories = $this->entityManager
                ->createNativeQuery('SELECT id, parent FROM category WHERE parent = :parentId ORDER BY id ASC', $rsm)
                ->setParameter('parentId', $parentId)
                ->execute()
            ;
        }

        return $categories;
    }
}

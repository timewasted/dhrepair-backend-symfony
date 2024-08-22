<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240822002227 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Remove unneeded category_hierarchy table. This can not be reverted!';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('DROP TABLE category_hierarchy');
    }

    public function down(Schema $schema): void
    {
        $this->throwIrreversibleMigrationException();
    }
}

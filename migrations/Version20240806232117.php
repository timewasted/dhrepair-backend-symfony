<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240806232117 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Remove obsolete columns from the user table. This can not be reverted!';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            ALTER TABLE user
                    DROP COLUMN salt
                ,   DROP COLUMN algorithm
                ,   DROP COLUMN work_factor
        ');
    }

    public function down(Schema $schema): void
    {
        $this->throwIrreversibleMigrationException();
    }
}

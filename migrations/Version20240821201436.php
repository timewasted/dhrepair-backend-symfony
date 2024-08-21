<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240821201436 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Change category table to support a nullable parent';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE category CHANGE COLUMN parent parent INT UNSIGNED DEFAULT NULL');
        $this->addSql('UPDATE category SET parent = NULL WHERE parent = 0');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('UPDATE category SET parent = 0 WHERE parent IS NULL');
        $this->addSql('ALTER TABLE category CHANGE COLUMN parent parent INT UNSIGNED DEFAULT 0 NOT NULL');
    }
}

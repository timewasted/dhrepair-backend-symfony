<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240904053519 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Move the contents of the cost_modifier table into the manufacturer table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE manufacturer ADD COLUMN cost_modifier DECIMAL(5,2) NOT NULL DEFAULT 1.00');
        $this->addSql('
            UPDATE manufacturer
            INNER JOIN cost_modifier ON cost_modifier.manufacturer_id = manufacturer.id
            SET manufacturer.cost_modifier = cost_modifier.modifier
        ');
        $this->addSql('DROP TABLE cost_modifier');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE cost_modifier (
                manufacturer_id INT UNSIGNED NOT NULL,
                modifier DECIMAL(5,2) NOT NULL DEFAULT 1.00,
                PRIMARY KEY (manufacturer_id),
                CONSTRAINT FK_1D9F9A48A23B42D FOREIGN KEY (manufacturer_id) REFERENCES manufacturer (id)
            )
        ');
        $this->addSql('
            INSERT INTO cost_modifier (manufacturer_id, modifier)
            SELECT id, cost_modifier FROM manufacturer
        ');
        $this->addSql('ALTER TABLE manufacturer DROP COLUMN cost_modifier');
    }
}

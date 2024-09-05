<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240905021150 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adjust the order_info table to clean up some legacy decisions. This can not be reverted!';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            ALTER TABLE order_info
                    CHANGE COLUMN username username VARCHAR(255) DEFAULT NULL
                ,   CHANGE COLUMN bill_company bill_company VARCHAR(100) DEFAULT NULL
                ,   CHANGE COLUMN bill_address_2 bill_address_2 VARCHAR(100) DEFAULT NULL
                ,   CHANGE COLUMN ship_company ship_company VARCHAR(100) DEFAULT NULL
                ,   CHANGE COLUMN ship_address_2 ship_address_2 VARCHAR(100) DEFAULT NULL
                ,   DROP COLUMN credit_card
        ');
        $this->addSql('UPDATE order_info SET username = NULL WHERE username = ""');
        $this->addSql('UPDATE order_info SET bill_company = NULL WHERE bill_company = ""');
        $this->addSql('UPDATE order_info SET bill_address_2 = NULL WHERE bill_address_2 = ""');
        $this->addSql('UPDATE order_info SET ship_company = NULL WHERE ship_company = ""');
        $this->addSql('UPDATE order_info SET ship_address_2 = NULL WHERE ship_address_2 = ""');
    }

    public function down(Schema $schema): void
    {
        $this->throwIrreversibleMigrationException();
    }
}

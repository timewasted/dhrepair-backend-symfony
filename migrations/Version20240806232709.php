<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240806232709 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add a table to store user auth tokens';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE user_auth_token (
                    user_id INT UNSIGNED NOT NULL
                ,   auth_token VARCHAR(255) NOT NULL
                ,   created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
                ,   KEY user_id (user_id)
                ,   UNIQUE KEY auth_token (auth_token)
                ,   CONSTRAINT user_auth_token_ibfk_1 FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE
            )
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE user_auth_token');
    }
}

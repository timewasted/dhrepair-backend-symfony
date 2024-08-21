<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240821220001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Update table schemas to more closely match what Doctrine would generate';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            ALTER TABLE access_log
                    CHANGE COLUMN timestamp timestamp DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'
        ');

        $this->addSql('
            ALTER TABLE category
                    CHANGE COLUMN description description LONGTEXT NOT NULL
                ,   CHANGE COLUMN modified_at modified_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'
                ,   ADD CONSTRAINT FK_64C19C13D8E604F FOREIGN KEY (parent) REFERENCES category (id)
        ');

        $this->addSql('SET FOREIGN_KEY_CHECKS = 0');
        $this->addSql('
            DELETE FROM category_closure
            WHERE parent = 0 AND child = 0 AND depth = 0
        ');
        $this->addSql('
            ALTER TABLE category_closure
                    ADD PRIMARY KEY (parent, child, depth)
                ,   ADD CONSTRAINT FK_8FDFCDAF3D8E604F FOREIGN KEY (parent) REFERENCES category (id)
                ,   ADD INDEX IDX_8FDFCDAF3D8E604F (parent)
        ');
        $this->addSql('SET FOREIGN_KEY_CHECKS = 1');

        $this->addSql('
            ALTER TABLE cost_modifier
                    ADD CONSTRAINT FK_1D9F9A48A23B42D FOREIGN KEY (manufacturer_id) REFERENCES manufacturer (id)
        ');

        $this->addSql('ALTER TABLE customer ADD PRIMARY KEY (user_id)');

        $this->addSql('
            ALTER TABLE image
                    CHANGE COLUMN added_at added_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'
        ');

        $this->addSql('
            ALTER TABLE item
                    CHANGE COLUMN description description LONGTEXT NOT NULL
                ,   CHANGE COLUMN availability_id availability_id INT UNSIGNED NOT NULL
                ,   CHANGE COLUMN modified_at modified_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'
                ,   ADD CONSTRAINT FK_1F1B251EA23B42D FOREIGN KEY (manufacturer_id) REFERENCES manufacturer (id)
                ,   ADD CONSTRAINT FK_1F1B251E61778466 FOREIGN KEY (availability_id) REFERENCES availability (id)
                ,   ADD INDEX availability_id (availability_id)
        ');

        $this->addSql('SET FOREIGN_KEY_CHECKS = 0');
        $this->addSql('
            ALTER TABLE item_category
                    DROP INDEX item_category
                ,   ADD CONSTRAINT FK_6A41D10A12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE
                ,   RENAME INDEX category_id TO IDX_6A41D10A12469DE2
        ');
        $this->addSql('SET FOREIGN_KEY_CHECKS = 1');

        $this->addSql('ALTER TABLE item_image ADD PRIMARY KEY (item_id, image_id)');

        $this->addSql('
            ALTER TABLE order_info
                    CHANGE COLUMN created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'
        ');

        $this->addSql('
            ALTER TABLE page_content
                    CHANGE COLUMN content content LONGTEXT NOT NULL
                ,   CHANGE COLUMN modified_at modified_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'
        ');

        $this->addSql('
            ALTER TABLE sf_session
                    CHANGE COLUMN id id VARBINARY(128) NOT NULL
                ,   CHANGE COLUMN data data LONGBLOB NOT NULL
                ,   RENAME INDEX EXPIRY TO lifetime_idx
        ');

        $this->addSql('
            ALTER TABLE tax_rate
                    CHANGE COLUMN state state CHAR(2) NOT NULL
                ,   ADD PRIMARY KEY (state)
        ');

        $this->addSql('
            ALTER TABLE transaction_log
                    CHANGE COLUMN created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'
                ,   ADD CONSTRAINT FK_747BDD0C8D9F6D38 FOREIGN KEY (order_id) REFERENCES `order_info` (id)
        ');

        $this->addSql('
            ALTER TABLE user
                    CHANGE COLUMN created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'
                ,   CHANGE COLUMN last_login last_login DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'
                ,   CHANGE COLUMN account_locked_until account_locked_until DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'
                ,   CHANGE COLUMN account_expires_at account_expires_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'
                ,   CHANGE COLUMN credentials_expire_at credentials_expire_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'
                ,   CHANGE COLUMN password_requested_at password_requested_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'
                ,   CHANGE COLUMN failed_login_attempts failed_login_attempts INT UNSIGNED DEFAULT 0 NOT NULL
        ');

        $this->addSql('SET FOREIGN_KEY_CHECKS = 0');
        $this->addSql('
            ALTER TABLE user_auth_token
                    CHANGE COLUMN created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'
                ,   DROP FOREIGN KEY user_auth_token_ibfk_1
                ,   ADD PRIMARY KEY (auth_token)
                ,   ADD CONSTRAINT FK_347236A2A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE
        ');
        $this->addSql('SET FOREIGN_KEY_CHECKS = 1');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('
            ALTER TABLE access_log
                    CHANGE COLUMN timestamp timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL
        ');

        $this->addSql('
            ALTER TABLE category
                    CHANGE COLUMN description description TEXT NOT NULL
                ,   CHANGE COLUMN modified_at modified_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL
                ,   DROP FOREIGN KEY FK_64C19C13D8E604F
        ');

        $this->addSql('SET FOREIGN_KEY_CHECKS = 0');
        $this->addSql('
            ALTER TABLE category_closure
                    DROP PRIMARY KEY
                ,   DROP FOREIGN KEY FK_8FDFCDAF3D8E604F
                ,   DROP INDEX IDX_8FDFCDAF3D8E604F
        ');
        $this->addSql('
            INSERT INTO category_closure
            (parent, child, depth) VALUES (0, 0, 0)
        ');
        $this->addSql('SET FOREIGN_KEY_CHECKS = 1');

        $this->addSql('ALTER TABLE cost_modifier DROP FOREIGN KEY FK_1D9F9A48A23B42D');

        $this->addSql('ALTER TABLE customer DROP PRIMARY KEY');

        $this->addSql('
            ALTER TABLE image
                    CHANGE COLUMN added_at added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL
        ');

        $this->addSql('
            ALTER TABLE item
                    CHANGE COLUMN description description TEXT NOT NULL
                ,   CHANGE COLUMN availability_id availability_id TINYINT UNSIGNED NOT NULL
                ,   CHANGE COLUMN modified_at modified_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL
                ,   DROP FOREIGN KEY FK_1F1B251EA23B42D
                ,   DROP FOREIGN KEY FK_1F1B251E61778466
                ,   DROP INDEX availability_id
        ');

        $this->addSql('SET FOREIGN_KEY_CHECKS = 0');
        $this->addSql('
            ALTER TABLE item_category
                    ADD INDEX item_category (item_id, category_id)
                ,   DROP FOREIGN KEY FK_6A41D10A12469DE2
                ,   RENAME INDEX IDX_6A41D10A12469DE2 TO category_id
        ');
        $this->addSql('SET FOREIGN_KEY_CHECKS = 1');

        $this->addSql('ALTER TABLE item_image DROP PRIMARY KEY');

        $this->addSql('
            ALTER TABLE order_info
                    CHANGE COLUMN created_at created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL
        ');

        $this->addSql('
            ALTER TABLE page_content
                    CHANGE COLUMN content content MEDIUMTEXT NOT NULL
                ,   CHANGE COLUMN modified_at modified_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL
        ');

        $this->addSql('
            ALTER TABLE sf_session
                    CHANGE COLUMN id id VARCHAR(255) NOT NULL
                ,   CHANGE COLUMN data data BLOB NOT NULL
                ,   RENAME INDEX lifetime_idx TO EXPIRY
        ');

        $this->addSql('
            ALTER TABLE tax_rate
                    CHANGE COLUMN state state CHAR(2) DEFAULT \'\' NOT NULL
                ,   DROP PRIMARY KEY
        ');

        $this->addSql('
            ALTER TABLE transaction_log
                    CHANGE COLUMN created_at created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL
                ,   DROP FOREIGN KEY FK_747BDD0C8D9F6D38
        ');

        $this->addSql('
            ALTER TABLE user
                    CHANGE COLUMN created_at created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL
                ,   CHANGE COLUMN last_login last_login TIMESTAMP DEFAULT NULL
                ,   CHANGE COLUMN account_locked_until account_locked_until TIMESTAMP DEFAULT NULL
                ,   CHANGE COLUMN account_expires_at account_expires_at TIMESTAMP DEFAULT NULL
                ,   CHANGE COLUMN credentials_expire_at credentials_expire_at TIMESTAMP DEFAULT NULL
                ,   CHANGE COLUMN password_requested_at password_requested_at TIMESTAMP DEFAULT NULL
                ,   CHANGE COLUMN failed_login_attempts failed_login_attempts INT UNSIGNED NOT NULL
        ');

        $this->addSql('SET FOREIGN_KEY_CHECKS = 0');
        $this->addSql('
            ALTER TABLE user_auth_token
                    CHANGE COLUMN created_at created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL
                ,   DROP PRIMARY KEY
                ,   DROP FOREIGN KEY FK_347236A2A76ED395
                ,   ADD CONSTRAINT user_auth_token_ibfk_1 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE
        ');
        $this->addSql('SET FOREIGN_KEY_CHECKS = 1');
    }
}

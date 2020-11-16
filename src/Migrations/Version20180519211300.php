<?php

/** @noinspection ALL */

/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 19.05.18
 * Time: 20:45
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Migrations\AbortMigrationException;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class Version20180519211300 extends AbstractMigration
{
    /**
     * @throws DBALException
     * @throws AbortMigrationException
     */
    public function up(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName());

        // Create api key table
        $this->addSql('CREATE TABLE api_key (
  api_key     VARCHAR(255) NOT NULL,
  user_id     INT DEFAULT NULL,
  valid_since DATETIME     NOT NULL,
  INDEX IDX_C912ED9DA76ED395 (user_id),
  PRIMARY KEY (api_key)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;');
        $this->addSql('ALTER TABLE api_key ADD CONSTRAINT FK_C912ED9DA76ED395 FOREIGN KEY (user_id) REFERENCES users (id);');

        // Drop indices
        $this->addSql('DROP INDEX UNIQ_1483A5E992FC23A8 ON users;');
        $this->addSql('DROP INDEX UNIQ_1483A5E9A0D96FBF ON users;');
        $this->addSql('DROP INDEX UNIQ_1483A5E9C05FB297 ON users;');

        // Alter user table and remove old columns
        $this->addSql('ALTER TABLE users DROP username, DROP username_canonical, DROP email_canonical;');

        // Alter user table and update column types
        $this->addSql('ALTER TABLE users CHANGE email email VARCHAR(255) NOT NULL, CHANGE password password LONGTEXT NOT NULL, CHANGE confirmation_token confirmation_token VARCHAR(255) DEFAULT NULL, CHANGE profile_picture profile_picture VARCHAR(255) DEFAULT NULL;');

        // Create unique index on users email
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email);');

        // Alter menuitem table
        $this->addSql('ALTER TABLE menu_item DROP FOREIGN KEY FK_D754D550727ACA70;');
        $this->addSql('ALTER TABLE menu_item ADD position INT NOT NULL;');
        $this->addSql('ALTER TABLE menu_item ADD CONSTRAINT FK_D754D550727ACA70 FOREIGN KEY (parent_id) REFERENCES menu_item (id) ON DELETE CASCADE;');

        // Alter table page
        $this->addSql("ALTER TABLE page ADD name VARCHAR(255) NOT NULL, CHANGE history history LONGTEXT NOT NULL COMMENT '(DC2Type:json)';");

        // Create unique index on page table for name
        $this->addSql('CREATE UNIQUE INDEX UNIQ_140AB6205E237E06 ON page (name);');

        // Alter gallery table
        $this->addSql('ALTER TABLE gallery CHANGE background background LONGTEXT DEFAULT NULL, CHANGE history history LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\';');

        // Alter route table
        $this->addSql('ALTER TABLE route DROP FOREIGN KEY FK_2C420799AB44FE0;');
        $this->addSql('ALTER TABLE route CHANGE menu_item_id menu_item_id INT NOT NULL;');
        $this->addSql('ALTER TABLE route ADD CONSTRAINT FK_2C420799AB44FE0 FOREIGN KEY (menu_item_id) REFERENCES menu_item (id) ON DELETE CASCADE;');

        // Alter table form item
        $this->addSql('ALTER TABLE form_item ADD position INT NOT NULL, CHANGE history history LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\';');
        $this->addSql('CREATE UNIQUE INDEX idx_form_item_position_form ON form_item (position, form_id);');

        // Alter table form
        $this->addSql('ALTER TABLE form ADD name VARCHAR(255) NOT NULL, CHANGE history history LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\';');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5288FD4F5E237E06 ON form (name);');

        // Move configuration to new table
        $this->addSql('CREATE TABLE configuration (
  id                        INT AUTO_INCREMENT NOT NULL,
  current_frontend_theme_id INT DEFAULT NULL,
  invalidate_api_key_after  INT                NOT NULL,
  UNIQUE INDEX UNIQ_A5E2A5D7C1E1F4D5 (current_frontend_theme_id),
  PRIMARY KEY (id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;');
        $this->addSql('ALTER TABLE configuration ADD CONSTRAINT FK_A5E2A5D7C1E1F4D5 FOREIGN KEY (current_frontend_theme_id) REFERENCES theme (id);');
        $this->addSql('INSERT INTO configuration (current_frontend_theme_id, invalidate_api_key_after) SELECT current_theme_id, 1 FROM frontend_configuration;');
        $this->addSql('DROP TABLE frontend_configuration;');
    }

    /**
     * @throws DBALException
     * @throws AbortMigrationException
     */
    public function down(Schema $schema): void
    {
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName());
    }
}

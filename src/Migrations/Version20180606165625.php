<?php

/** @noinspection ALL */

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180606165625 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            'mysql' !== $this->connection->getDatabasePlatform()->getName(),
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('CREATE TABLE video (id INT AUTO_INCREMENT NOT NULL, creator_id INT DEFAULT NULL, updated_by_id INT DEFAULT NULL, history LONGTEXT NOT NULL, created_at DATETIME NOT NULL, last_updated_at DATETIME NOT NULL, description LONGTEXT NOT NULL, video VARCHAR(255), slug VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_7CC7DA2C989D9B62 (slug), UNIQUE INDEX UNIQ_7CC7DA2C5E237E06 (name), INDEX IDX_7CC7DA2C61220EA6 (creator_id), INDEX IDX_7CC7DA2C896DBBDE (updated_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE video ADD CONSTRAINT FK_7CC7DA2C61220EA6 FOREIGN KEY (creator_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE video ADD CONSTRAINT FK_7CC7DA2C896DBBDE FOREIGN KEY (updated_by_id) REFERENCES users (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            'mysql' !== $this->connection->getDatabasePlatform()->getName(),
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('DROP TABLE video');
        $this->addSql('ALTER TABLE access_log CHANGE created_at created_at DATETIME DEFAULT \'2017-10-31 00:00:00\' NOT NULL');
    }
}

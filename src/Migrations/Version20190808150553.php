<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190808150553 extends AbstractMigration
{
    /** @noinspection SenselessMethodDuplicationInspection */
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            'mysql' !== $this->connection->getDatabasePlatform()->getName(),
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, form_id INT DEFAULT NULL, data LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\', target_address VARCHAR(255) NOT NULL, INDEX IDX_B6BD307F5FF69B7D (form_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

ALTER TABLE message ADD CONSTRAINT FK_B6BD307F5FF69B7D FOREIGN KEY (form_id) REFERENCES form (id)');
        $this->addSql('# noinspection SqlResolveForFile

ALTER TABLE configuration ADD messaging_center_enabled TINYINT(1) NOT NULL DEFAULT 1');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            'mysql' !== $this->connection->getDatabasePlatform()->getName(),
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('# noinspection SqlResolveForFile

DROP TABLE message');
        $this->addSql('# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

ALTER TABLE configuration DROP messaging_center_enabled');
    }
}

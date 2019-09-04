<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190518000648 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->abortIf(
            'mysql' !== $this->connection->getDatabasePlatform()->getName(),
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('# noinspection SqlResolveForFile

ALTER TABLE users CHANGE firstname firstname VARCHAR(255) DEFAULT NULL, CHANGE lastname lastname VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->abortIf(
            'mysql' !== $this->connection->getDatabasePlatform()->getName(),
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('# noinspection SqlResolveForFile

ALTER TABLE users CHANGE firstname firstname VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE lastname lastname VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');
    }
}

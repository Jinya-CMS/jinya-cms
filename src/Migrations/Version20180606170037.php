<?php

/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180606170037 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE access_log');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE access_log (id INT AUTO_INCREMENT NOT NULL, client_ip VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, uri VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, method VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, query_string VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, request LONGTEXT NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:array)\', user_agent VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, created_at DATETIME DEFAULT \'2017-10-31 00:00:00\' NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    }
}

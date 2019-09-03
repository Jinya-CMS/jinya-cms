<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190813194535 extends AbstractMigration
{
    /** @noinspection SenselessMethodDuplicationInspection */
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

ALTER TABLE message ADD subject VARCHAR(255) NOT NULL, ADD content LONGTEXT NOT NULL, ADD from_address VARCHAR(255) NOT NULL, ADD send_at DATETIME NOT NULL, DROP data');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE message ADD data LONGTEXT NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:json_array)\', DROP subject, DROP content, DROP from_address, DROP send_at');
    }
}

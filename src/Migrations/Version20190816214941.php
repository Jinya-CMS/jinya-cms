<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190816214941 extends AbstractMigration
{
    /** @noinspection SenselessMethodDuplicationInspection */
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        /* @noinspection PhpUnhandledExceptionInspection */
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(),
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('# noinspection SqlResolveForFile

ALTER TABLE message ADD is_read TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        /* @noinspection PhpUnhandledExceptionInspection */
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(),
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

ALTER TABLE message DROP is_read');
    }
}
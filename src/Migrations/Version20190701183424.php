<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190701183424 extends AbstractMigration
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
        $this->abortIf(
            'mysql' !== $this->connection->getDatabasePlatform()->getName(),
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('# noinspection SqlResolveForFile

ALTER TABLE segment ADD position INT NOT NULL, ADD action VARCHAR(255) DEFAULT NULL, ADD script VARCHAR(255) DEFAULT NULL, ADD target VARCHAR(255) DEFAULT NULL, CHANGE html html VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        /* @noinspection PhpUnhandledExceptionInspection */
        $this->abortIf(
            'mysql' !== $this->connection->getDatabasePlatform()->getName(),
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

ALTER TABLE segment DROP position, DROP action, DROP script, DROP target, CHANGE html html VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');
    }
}

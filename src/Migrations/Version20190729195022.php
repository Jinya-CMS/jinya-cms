<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190729195022 extends AbstractMigration
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

        $this->addSql('CREATE TABLE theme_segment_page (name VARCHAR(255) NOT NULL, theme_id INT NOT NULL, segment_page_id INT NOT NULL, INDEX IDX_5BFE07FF59027487 (theme_id), INDEX IDX_5BFE07FF533F2206 (segment_page_id), PRIMARY KEY(theme_id, segment_page_id, name)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

ALTER TABLE theme_segment_page ADD CONSTRAINT FK_5BFE07FF59027487 FOREIGN KEY (theme_id) REFERENCES theme (id)');
        $this->addSql('# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

ALTER TABLE theme_segment_page ADD CONSTRAINT FK_5BFE07FF533F2206 FOREIGN KEY (segment_page_id) REFERENCES segment_page (id)');
        $this->addSql('ALTER TABLE form_item ADD spam_filter LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json_array)\'');
        $this->addSql(' WHERE id > 0');
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

DROP TABLE theme_segment_page');
    }
}

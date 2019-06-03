<?php

/** @noinspection ALL */

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180608162725 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            'mysql' !== $this->connection->getDatabasePlatform()->getName(),
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE uploading_video ADD video_id INT DEFAULT NULL, DROP video');
        $this->addSql('ALTER TABLE uploading_video ADD CONSTRAINT FK_B333C9A529C1004E FOREIGN KEY (video_id) REFERENCES video (id)');
        $this->addSql('CREATE INDEX IDX_B333C9A529C1004E ON uploading_video (video_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            'mysql' !== $this->connection->getDatabasePlatform()->getName(),
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE uploading_video DROP FOREIGN KEY FK_B333C9A529C1004E');
        $this->addSql('DROP INDEX IDX_B333C9A529C1004E ON uploading_video');
        $this->addSql('ALTER TABLE uploading_video ADD video VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, DROP video_id');
    }
}

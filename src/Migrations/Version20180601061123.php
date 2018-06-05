<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180601061123 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE youtube_video (id INT AUTO_INCREMENT NOT NULL, creator_id INT DEFAULT NULL, updated_by_id INT DEFAULT NULL, history LONGTEXT NOT NULL, created_at DATETIME NOT NULL, last_updated_at DATETIME NOT NULL, description LONGTEXT NOT NULL, videokey VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_AE4DCDC9989D9B62 (slug), UNIQUE INDEX UNIQ_AE4DCDC95E237E06 (name), INDEX IDX_AE4DCDC961220EA6 (creator_id), INDEX IDX_AE4DCDC9896DBBDE (updated_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE youtube_video ADD CONSTRAINT FK_AE4DCDC961220EA6 FOREIGN KEY (creator_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE youtube_video ADD CONSTRAINT FK_AE4DCDC9896DBBDE FOREIGN KEY (updated_by_id) REFERENCES users (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE youtube_video');
    }
}

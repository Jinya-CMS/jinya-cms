<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190529171232 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        /* @noinspection PhpUnhandledExceptionInspection */
        $this->abortIf(
            'mysql' !== $this->connection->getDatabasePlatform()->getName(),
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('CREATE TABLE segment_page (id INT AUTO_INCREMENT NOT NULL, creator_id INT DEFAULT NULL, updated_by_id INT DEFAULT NULL, history LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', created_at DATETIME NOT NULL, last_updated_at DATETIME NOT NULL, slug VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_CBA9317D989D9B62 (slug), UNIQUE INDEX UNIQ_CBA9317D5E237E06 (name), INDEX IDX_CBA9317D61220EA6 (creator_id), INDEX IDX_CBA9317D896DBBDE (updated_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE segment (id INT AUTO_INCREMENT NOT NULL, page_id INT DEFAULT NULL, artwork_id INT DEFAULT NULL, form_id INT DEFAULT NULL, video_id INT DEFAULT NULL, youtube_video_id INT DEFAULT NULL, art_gallery_id INT DEFAULT NULL, video_gallery_id INT DEFAULT NULL, html VARCHAR(255) NOT NULL, INDEX IDX_1881F565C4663E4 (page_id), INDEX IDX_1881F565DB8FFA4 (artwork_id), INDEX IDX_1881F5655FF69B7D (form_id), INDEX IDX_1881F56529C1004E (video_id), INDEX IDX_1881F5658E06FC7F (youtube_video_id), INDEX IDX_1881F56531A68AC7 (art_gallery_id), INDEX IDX_1881F5658440B739 (video_gallery_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

ALTER TABLE segment_page ADD CONSTRAINT FK_CBA9317D61220EA6 FOREIGN KEY (creator_id) REFERENCES users (id)');
        $this->addSql('# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

ALTER TABLE segment_page ADD CONSTRAINT FK_CBA9317D896DBBDE FOREIGN KEY (updated_by_id) REFERENCES users (id)');
        $this->addSql('# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

ALTER TABLE segment ADD CONSTRAINT FK_1881F565C4663E4 FOREIGN KEY (page_id) REFERENCES segment_page (id)');
        $this->addSql('# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

ALTER TABLE segment ADD CONSTRAINT FK_1881F565DB8FFA4 FOREIGN KEY (artwork_id) REFERENCES artwork (id)');
        $this->addSql('# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

ALTER TABLE segment ADD CONSTRAINT FK_1881F5655FF69B7D FOREIGN KEY (form_id) REFERENCES form (id)');
        $this->addSql('# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

ALTER TABLE segment ADD CONSTRAINT FK_1881F56529C1004E FOREIGN KEY (video_id) REFERENCES video (id)');
        $this->addSql('# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

ALTER TABLE segment ADD CONSTRAINT FK_1881F5658E06FC7F FOREIGN KEY (youtube_video_id) REFERENCES youtube_video (id)');
        $this->addSql('# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

ALTER TABLE segment ADD CONSTRAINT FK_1881F56531A68AC7 FOREIGN KEY (art_gallery_id) REFERENCES art_gallery (id)');
        $this->addSql('# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

ALTER TABLE segment ADD CONSTRAINT FK_1881F5658440B739 FOREIGN KEY (video_gallery_id) REFERENCES art_gallery (id)');
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

ALTER TABLE segment DROP FOREIGN KEY FK_1881F565C4663E4');
        $this->addSql('# noinspection SqlResolveForFile

DROP TABLE segment_page');
        $this->addSql('# noinspection SqlResolveForFile

DROP TABLE segment');
    }
}

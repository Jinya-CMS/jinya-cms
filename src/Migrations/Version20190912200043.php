<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190912200043 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            'mysql' !== $this->connection->getDatabasePlatform()->getName(),
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F3610162CB942');
        $this->addSql('ALTER TABLE folder DROP FOREIGN KEY FK_ECA209CD727ACA70');
        $this->addSql('ALTER TABLE tag_folder DROP FOREIGN KEY FK_5E340E1162CB942');
        $this->addSql('CREATE TABLE uploading_file (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', file_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_C219262693CB796C (file_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE uploading_file_chunk (id INT AUTO_INCREMENT NOT NULL, uploading_file_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', chunk_path VARCHAR(255) NOT NULL, chunk_position INT NOT NULL, INDEX IDX_3F70FB06FD34D444 (uploading_file_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE uploading_file ADD CONSTRAINT FK_C219262693CB796C FOREIGN KEY (file_id) REFERENCES file (id)');
        $this->addSql('ALTER TABLE uploading_file_chunk ADD CONSTRAINT FK_3F70FB06FD34D444 FOREIGN KEY (uploading_file_id) REFERENCES uploading_file (id)');
        $this->addSql('DROP TABLE folder');
        $this->addSql('DROP TABLE tag_folder');
        $this->addSql('DROP INDEX IDX_8C9F3610162CB942 ON file');
        $this->addSql('DROP INDEX folder_and_name ON file');
        $this->addSql('ALTER TABLE file DROP folder_id');
        $this->addSql('CREATE UNIQUE INDEX name ON file (name)');
        $this->addSql('ALTER TABLE gallery ADD type VARCHAR(255) NOT NULL, ADD orientation VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            'mysql' !== $this->connection->getDatabasePlatform()->getName(),
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE uploading_file_chunk DROP FOREIGN KEY FK_3F70FB06FD34D444');
        $this->addSql('CREATE TABLE folder (id INT AUTO_INCREMENT NOT NULL, creator_id INT DEFAULT NULL, updated_by_id INT DEFAULT NULL, parent_id INT DEFAULT NULL, history LONGTEXT NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:json)\', created_at DATETIME NOT NULL, last_updated_at DATETIME NOT NULL, name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, INDEX IDX_ECA209CD896DBBDE (updated_by_id), INDEX IDX_ECA209CD727ACA70 (parent_id), INDEX IDX_ECA209CD61220EA6 (creator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE tag_folder (tag_id INT NOT NULL, folder_id INT NOT NULL, INDEX IDX_5E340E1BAD26311 (tag_id), INDEX IDX_5E340E1162CB942 (folder_id), PRIMARY KEY(tag_id, folder_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE folder ADD CONSTRAINT FK_ECA209CD61220EA6 FOREIGN KEY (creator_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE folder ADD CONSTRAINT FK_ECA209CD727ACA70 FOREIGN KEY (parent_id) REFERENCES folder (id)');
        $this->addSql('ALTER TABLE folder ADD CONSTRAINT FK_ECA209CD896DBBDE FOREIGN KEY (updated_by_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE tag_folder ADD CONSTRAINT FK_5E340E1162CB942 FOREIGN KEY (folder_id) REFERENCES folder (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tag_folder ADD CONSTRAINT FK_5E340E1BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE uploading_file');
        $this->addSql('DROP TABLE uploading_file_chunk');
        $this->addSql('DROP INDEX name ON file');
        $this->addSql('ALTER TABLE file ADD folder_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F3610162CB942 FOREIGN KEY (folder_id) REFERENCES folder (id)');
        $this->addSql('CREATE INDEX IDX_8C9F3610162CB942 ON file (folder_id)');
        $this->addSql('CREATE UNIQUE INDEX folder_and_name ON file (folder_id, name)');
        $this->addSql('ALTER TABLE gallery DROP type, DROP orientation');
    }
}

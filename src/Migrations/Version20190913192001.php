<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190913192001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
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
        $this->addSql('ALTER TABLE menu_item ADD CONSTRAINT FK_D754D550727ACA70 FOREIGN KEY (parent_id) REFERENCES menu_item (id) ON DELETE CASCADE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_140AB6205E237E06 ON page (name)');
        $this->addSql('ALTER TABLE segment CHANGE html html VARCHAR(255) DEFAULT NULL, CHANGE action action LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE theme_art_gallery DROP FOREIGN KEY FK_1B446A49F025C27');
        $this->addSql('DROP INDEX fk_1b446a49f025c27 ON theme_art_gallery');
        $this->addSql('CREATE INDEX IDX_1B446A4931A68AC7 ON theme_art_gallery (art_gallery_id)');
        $this->addSql('ALTER TABLE theme_art_gallery ADD CONSTRAINT FK_1B446A49F025C27 FOREIGN KEY (art_gallery_id) REFERENCES art_gallery (id)');
        $this->addSql('ALTER TABLE theme_artwork DROP FOREIGN KEY FK_D17CC055C3853658');
        $this->addSql('DROP INDEX fk_d17cc055c3853658 ON theme_artwork');
        $this->addSql('CREATE INDEX IDX_D17CC055DB8FFA4 ON theme_artwork (artwork_id)');
        $this->addSql('ALTER TABLE theme_artwork ADD CONSTRAINT FK_D17CC055C3853658 FOREIGN KEY (artwork_id) REFERENCES artwork (id)');
        $this->addSql('ALTER TABLE theme_form DROP FOREIGN KEY FK_184F0972EB1E44FA');
        $this->addSql('DROP INDEX fk_184f0972eb1e44fa ON theme_form');
        $this->addSql('CREATE INDEX IDX_184F09725FF69B7D ON theme_form (form_id)');
        $this->addSql('ALTER TABLE theme_form ADD CONSTRAINT FK_184F0972EB1E44FA FOREIGN KEY (form_id) REFERENCES form (id)');
        $this->addSql('ALTER TABLE theme_menu DROP FOREIGN KEY FK_37C2CEAED45BC22E');
        $this->addSql('DROP INDEX fk_37c2ceaed45bc22e ON theme_menu');
        $this->addSql('CREATE INDEX IDX_37C2CEAECCD7E912 ON theme_menu (menu_id)');
        $this->addSql('ALTER TABLE theme_menu ADD CONSTRAINT FK_37C2CEAED45BC22E FOREIGN KEY (menu_id) REFERENCES menu (id)');
        $this->addSql('ALTER TABLE theme_page DROP FOREIGN KEY FK_5ECD421D6671BEF3');
        $this->addSql('ALTER TABLE theme_page DROP FOREIGN KEY FK_5ECD421D6671BEF3');
        $this->addSql('ALTER TABLE theme_page ADD CONSTRAINT FK_5ECD421DC4663E4 FOREIGN KEY (page_id) REFERENCES page (id)');
        $this->addSql('DROP INDEX fk_5ecd421d6671bef3 ON theme_page');
        $this->addSql('CREATE INDEX IDX_5ECD421DC4663E4 ON theme_page (page_id)');
        $this->addSql('ALTER TABLE theme_page ADD CONSTRAINT FK_5ECD421D6671BEF3 FOREIGN KEY (page_id) REFERENCES form (id)');
        $this->addSql('ALTER TABLE theme_video_gallery DROP FOREIGN KEY FK_A1E024ADB7BF6C63');
        $this->addSql('DROP INDEX fk_a1e024adb7bf6c63 ON theme_video_gallery');
        $this->addSql('CREATE INDEX IDX_A1E024AD8440B739 ON theme_video_gallery (video_gallery_id)');
        $this->addSql('ALTER TABLE theme_video_gallery ADD CONSTRAINT FK_A1E024ADB7BF6C63 FOREIGN KEY (video_gallery_id) REFERENCES video_gallery (id)');
        $this->addSql('ALTER TABLE video CHANGE history history LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE youtube_video CHANGE history history LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', CHANGE videokey video_key VARCHAR(255) NOT NULL');
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
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE uploading_file_chunk DROP FOREIGN KEY FK_3F70FB06FD34D444');
        $this->addSql('CREATE TABLE folder (id INT AUTO_INCREMENT NOT NULL, creator_id INT DEFAULT NULL, updated_by_id INT DEFAULT NULL, parent_id INT DEFAULT NULL, history LONGTEXT NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:json)\', created_at DATETIME NOT NULL, last_updated_at DATETIME NOT NULL, name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, INDEX IDX_ECA209CD896DBBDE (updated_by_id), INDEX IDX_ECA209CD61220EA6 (creator_id), INDEX IDX_ECA209CD727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
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
        $this->addSql('ALTER TABLE menu_item DROP FOREIGN KEY FK_D754D550727ACA70');
        $this->addSql('DROP INDEX UNIQ_140AB6205E237E06 ON page');
        $this->addSql('ALTER TABLE segment CHANGE html html LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE action action VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE theme_art_gallery DROP FOREIGN KEY FK_1B446A4931A68AC7');
        $this->addSql('DROP INDEX idx_1b446a4931a68ac7 ON theme_art_gallery');
        $this->addSql('CREATE INDEX FK_1B446A49F025C27 ON theme_art_gallery (art_gallery_id)');
        $this->addSql('ALTER TABLE theme_art_gallery ADD CONSTRAINT FK_1B446A4931A68AC7 FOREIGN KEY (art_gallery_id) REFERENCES art_gallery (id)');
        $this->addSql('ALTER TABLE theme_artwork DROP FOREIGN KEY FK_D17CC055DB8FFA4');
        $this->addSql('DROP INDEX idx_d17cc055db8ffa4 ON theme_artwork');
        $this->addSql('CREATE INDEX FK_D17CC055C3853658 ON theme_artwork (artwork_id)');
        $this->addSql('ALTER TABLE theme_artwork ADD CONSTRAINT FK_D17CC055DB8FFA4 FOREIGN KEY (artwork_id) REFERENCES artwork (id)');
        $this->addSql('ALTER TABLE theme_form DROP FOREIGN KEY FK_184F09725FF69B7D');
        $this->addSql('DROP INDEX idx_184f09725ff69b7d ON theme_form');
        $this->addSql('CREATE INDEX FK_184F0972EB1E44FA ON theme_form (form_id)');
        $this->addSql('ALTER TABLE theme_form ADD CONSTRAINT FK_184F09725FF69B7D FOREIGN KEY (form_id) REFERENCES form (id)');
        $this->addSql('ALTER TABLE theme_menu DROP FOREIGN KEY FK_37C2CEAECCD7E912');
        $this->addSql('DROP INDEX idx_37c2ceaeccd7e912 ON theme_menu');
        $this->addSql('CREATE INDEX FK_37C2CEAED45BC22E ON theme_menu (menu_id)');
        $this->addSql('ALTER TABLE theme_menu ADD CONSTRAINT FK_37C2CEAECCD7E912 FOREIGN KEY (menu_id) REFERENCES menu (id)');
        $this->addSql('ALTER TABLE theme_page DROP FOREIGN KEY FK_5ECD421DC4663E4');
        $this->addSql('ALTER TABLE theme_page DROP FOREIGN KEY FK_5ECD421DC4663E4');
        $this->addSql('ALTER TABLE theme_page ADD CONSTRAINT FK_5ECD421D6671BEF3 FOREIGN KEY (page_id) REFERENCES form (id)');
        $this->addSql('DROP INDEX idx_5ecd421dc4663e4 ON theme_page');
        $this->addSql('CREATE INDEX FK_5ECD421D6671BEF3 ON theme_page (page_id)');
        $this->addSql('ALTER TABLE theme_page ADD CONSTRAINT FK_5ECD421DC4663E4 FOREIGN KEY (page_id) REFERENCES page (id)');
        $this->addSql('ALTER TABLE theme_video_gallery DROP FOREIGN KEY FK_A1E024AD8440B739');
        $this->addSql('DROP INDEX idx_a1e024ad8440b739 ON theme_video_gallery');
        $this->addSql('CREATE INDEX FK_A1E024ADB7BF6C63 ON theme_video_gallery (video_gallery_id)');
        $this->addSql('ALTER TABLE theme_video_gallery ADD CONSTRAINT FK_A1E024AD8440B739 FOREIGN KEY (video_gallery_id) REFERENCES video_gallery (id)');
        $this->addSql('ALTER TABLE video CHANGE history history LONGTEXT NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE youtube_video CHANGE history history LONGTEXT NOT NULL COLLATE utf8_unicode_ci, CHANGE video_key videokey VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');
    }
}

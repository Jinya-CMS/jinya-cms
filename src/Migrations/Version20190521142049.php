<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Throwable;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190521142049 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->abortIf(
            'mysql' !== $this->connection->getDatabasePlatform()->getName(),
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('CREATE TABLE theme_menu (theme_id INT NOT NULL, menu_id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(theme_id, menu_id, name)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE theme_page (theme_id INT NOT NULL, page_id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(theme_id, page_id, name)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE theme_form (theme_id INT NOT NULL, form_id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(theme_id, form_id, name)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE theme_art_gallery (theme_id INT NOT NULL, art_gallery_id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(theme_id, art_gallery_id, name)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE theme_video_gallery (theme_id INT NOT NULL, video_gallery_id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(theme_id, video_gallery_id, name)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE theme_artwork (theme_id INT NOT NULL, artwork_id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(theme_id, artwork_id, name)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

ALTER TABLE theme_menu ADD CONSTRAINT FK_37C2CEAE7675842B FOREIGN KEY (theme_id) REFERENCES theme (id)');
        $this->addSql('# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

ALTER TABLE theme_menu ADD CONSTRAINT FK_37C2CEAED45BC22E FOREIGN KEY (menu_id) REFERENCES menu (id)');
        $this->addSql('# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

ALTER TABLE theme_page ADD CONSTRAINT FK_5ECD421DDABBC69D FOREIGN KEY (theme_id) REFERENCES theme (id)');
        $this->addSql('# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

ALTER TABLE theme_page ADD CONSTRAINT FK_5ECD421D6671BEF3 FOREIGN KEY (page_id) REFERENCES form (id)');
        $this->addSql('# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

ALTER TABLE theme_form ADD CONSTRAINT FK_184F0972EB1E44F9 FOREIGN KEY (theme_id) REFERENCES theme (id)');
        $this->addSql('# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

ALTER TABLE theme_form ADD CONSTRAINT FK_184F0972EB1E44FA FOREIGN KEY (form_id) REFERENCES form (id)');
        $this->addSql('# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

ALTER TABLE theme_art_gallery ADD CONSTRAINT FK_1B446A499D8D9BFA FOREIGN KEY (theme_id) REFERENCES theme (id)');
        $this->addSql('# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

ALTER TABLE theme_art_gallery ADD CONSTRAINT FK_1B446A49F025C27 FOREIGN KEY (art_gallery_id) REFERENCES art_gallery (id)');
        $this->addSql('# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

ALTER TABLE theme_video_gallery ADD CONSTRAINT FK_A1E024ADCEF55CAC FOREIGN KEY (theme_id) REFERENCES theme (id)');
        $this->addSql('# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

ALTER TABLE theme_video_gallery ADD CONSTRAINT FK_A1E024ADB7BF6C63 FOREIGN KEY (video_gallery_id) REFERENCES video_gallery (id)');
        $this->addSql('# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

ALTER TABLE theme_artwork ADD CONSTRAINT FK_D17CC055C70B073C FOREIGN KEY (theme_id) REFERENCES theme (id)');
        $this->addSql('# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

ALTER TABLE theme_artwork ADD CONSTRAINT FK_D17CC055C3853658 FOREIGN KEY (artwork_id) REFERENCES artwork (id)');

        $themes = $this->connection->fetchAll('# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

SELECT id, primary_menu_id, secondary_menu_id, footer_menu_id FROM theme');
        foreach ($themes as $theme) {
            try {
                $id = $theme['id'];
                $primaryMenu = $theme['primary_menu_id'];
                $secondaryMenu = $theme['secondary_menu_id'];
                $footerMenu = $theme['footer_menu_id'];

                if (null !== $primaryMenu) {
                    $this->addSql("# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

INSERT INTO theme_menu (theme_id, menu_id, name) VALUES ($id, $primaryMenu, 'primary')");
                }
                if (null !== $secondaryMenu) {
                    $this->addSql("# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

INSERT INTO theme_menu (theme_id, menu_id, name) VALUES ($id, $secondaryMenu, 'secondary')");
                }
                if (null !== $footerMenu) {
                    $this->addSql("# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

INSERT INTO theme_menu (theme_id, menu_id, name) VALUES ($id, $footerMenu, 'footer')");
                }
            } catch (Throwable $exception) {
                $this->abortIf(true);
            }
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->abortIf(
            'mysql' !== $this->connection->getDatabasePlatform()->getName(),
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('# noinspection SqlResolveForFile

DROP TABLE theme_menu');
        $this->addSql('# noinspection SqlResolveForFile

DROP TABLE theme_page');
        $this->addSql('# noinspection SqlResolveForFile

DROP TABLE theme_form');
        $this->addSql('# noinspection SqlResolveForFile

DROP TABLE theme_art_gallery');
        $this->addSql('# noinspection SqlResolveForFile

DROP TABLE theme_video_gallery');
        $this->addSql('# noinspection SqlResolveForFile

DROP TABLE theme_artwork');
        $this->addSql('# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

ALTER TABLE art_gallery DROP FOREIGN KEY FK_F36F794661220EA6');
        $this->addSql('# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

ALTER TABLE art_gallery DROP FOREIGN KEY FK_F36F7946896DBBDE');
        $this->addSql('# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

DROP INDEX idx_f36f7946896dbbde ON art_gallery');
        $this->addSql('# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

CREATE INDEX IDX_472B783A896DBBDE ON art_gallery (updated_by_id)');
        $this->addSql('# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

DROP INDEX uniq_f36f7946989d9b62 ON art_gallery');
        $this->addSql('# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

CREATE UNIQUE INDEX UNIQ_472B783A989D9B62 ON art_gallery (slug)');
        $this->addSql('# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

DROP INDEX idx_f36f794661220ea6 ON art_gallery');
        $this->addSql('# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

CREATE INDEX IDX_472B783A61220EA6 ON art_gallery (creator_id)');
        $this->addSql('# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

DROP INDEX uniq_f36f79465e237e06 ON art_gallery');
        $this->addSql('# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

CREATE UNIQUE INDEX UNIQ_472B783A5E237E06 ON art_gallery (name)');
        $this->addSql('# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

ALTER TABLE art_gallery ADD CONSTRAINT FK_F36F794661220EA6 FOREIGN KEY (creator_id) REFERENCES users (id)');
        $this->addSql('# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

ALTER TABLE art_gallery ADD CONSTRAINT FK_F36F7946896DBBDE FOREIGN KEY (updated_by_id) REFERENCES users (id)');
        $this->addSql('# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

ALTER TABLE art_gallery_label DROP FOREIGN KEY FK_7D9BFD6733B92F39');
        $this->addSql('# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

DROP INDEX idx_7d9bfd6733b92f39 ON art_gallery_label');
        $this->addSql('# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

CREATE INDEX IDX_EA3A80CB33B92F39 ON art_gallery_label (label_id)');
        $this->addSql('# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

ALTER TABLE art_gallery_label ADD CONSTRAINT FK_7D9BFD6733B92F39 FOREIGN KEY (label_id) REFERENCES `label` (id) ON DELETE CASCADE');
        $this->addSql('# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

DROP INDEX idx_form_item_position_form ON form_item');
        $this->addSql('# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

ALTER TABLE menu_item DROP FOREIGN KEY FK_D754D550727ACA70');
        $this->addSql('# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

DROP INDEX UNIQ_140AB6205E237E06 ON page');
        $this->addSql('# noinspection SqlResolveForFile

ALTER TABLE users CHANGE about_me about_me LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci');
        $this->addSql('# noinspection SqlResolveForFile

ALTER TABLE video CHANGE history history LONGTEXT NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('# noinspection SqlResolveForFile

ALTER TABLE video_gallery CHANGE history history LONGTEXT NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('# noinspection SqlResolveForFile

# noinspection SqlResolveForFile

ALTER TABLE youtube_video CHANGE history history LONGTEXT NOT NULL COLLATE utf8_unicode_ci, CHANGE video_key videokey VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');
    }
}

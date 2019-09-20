<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190910153236 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(),
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE gallery (id INT AUTO_INCREMENT NOT NULL, creator_id INT DEFAULT NULL, updated_by_id INT DEFAULT NULL, history LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', created_at DATETIME NOT NULL, last_updated_at DATETIME NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_472B783A989D9B62 (slug), INDEX IDX_472B783A61220EA6 (creator_id), INDEX IDX_472B783A896DBBDE (updated_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE gallery_file_position (id INT AUTO_INCREMENT NOT NULL, gallery_id INT DEFAULT NULL, file_id INT DEFAULT NULL, position INT NOT NULL, INDEX IDX_57EA691A4E7AF8F (gallery_id), INDEX IDX_57EA691A93CB796C (file_id), UNIQUE INDEX gallery_and_position (gallery_id, position), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE gallery ADD CONSTRAINT FK_472B783A61220EA7 FOREIGN KEY (creator_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE gallery ADD CONSTRAINT FK_472B783A896DBBDF FOREIGN KEY (updated_by_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE gallery_file_position ADD CONSTRAINT FK_57EA691A4E7AF8F FOREIGN KEY (gallery_id) REFERENCES gallery (id)');
        $this->addSql('ALTER TABLE gallery_file_position ADD CONSTRAINT FK_57EA691A93CB796C FOREIGN KEY (file_id) REFERENCES file (id)');
        $this->addSql('ALTER TABLE file ADD galleries_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F3610BA96467C FOREIGN KEY (galleries_id) REFERENCES gallery_file_position (id)');
        $this->addSql('CREATE INDEX IDX_8C9F3610BA96467C ON file (galleries_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(),
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE gallery_file_position DROP FOREIGN KEY FK_57EA691A4E7AF8F');
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F3610BA96467C');
        $this->addSql('DROP TABLE gallery');
        $this->addSql('DROP TABLE gallery_file_position');
        $this->addSql('DROP INDEX IDX_8C9F3610BA96467C ON file');
        $this->addSql('ALTER TABLE file DROP galleries_id');
    }
}

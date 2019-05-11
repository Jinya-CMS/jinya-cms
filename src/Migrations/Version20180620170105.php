<?php

/** @noinspection ALL */

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180620170105 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE art_gallery_label DROP FOREIGN KEY FK_EA3A80CB4E7AF8F');
        $this->addSql('DROP INDEX IDX_EA3A80CB4E7AF8F ON art_gallery_label');
        $this->addSql('ALTER TABLE art_gallery_label DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE art_gallery_label CHANGE gallery_id art_gallery_id INT NOT NULL');
        $this->addSql('ALTER TABLE art_gallery_label ADD CONSTRAINT FK_7D9BFD6731A68AC7 FOREIGN KEY (art_gallery_id) REFERENCES art_gallery (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_7D9BFD6731A68AC7 ON art_gallery_label (art_gallery_id)');
        $this->addSql('ALTER TABLE art_gallery_label ADD PRIMARY KEY (art_gallery_id, label_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE art_gallery_label DROP FOREIGN KEY FK_7D9BFD6731A68AC7');
        $this->addSql('DROP INDEX IDX_7D9BFD6731A68AC7 ON art_gallery_label');
        $this->addSql('ALTER TABLE art_gallery_label DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE art_gallery_label CHANGE art_gallery_id gallery_id INT NOT NULL');
        $this->addSql('ALTER TABLE art_gallery_label ADD CONSTRAINT FK_EA3A80CB4E7AF8F FOREIGN KEY (gallery_id) REFERENCES art_gallery (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_EA3A80CB4E7AF8F ON art_gallery_label (gallery_id)');
        $this->addSql('ALTER TABLE art_gallery_label ADD PRIMARY KEY (gallery_id, label_id)');
    }
}

<?php /** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180620170105 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE art_gallery RENAME INDEX uniq_472b783a5e237e06 TO UNIQ_F36F79465E237E06');
        $this->addSql('ALTER TABLE art_gallery RENAME INDEX uniq_472b783a989d9b62 TO UNIQ_F36F7946989D9B62');
        $this->addSql('ALTER TABLE art_gallery RENAME INDEX idx_472b783a61220ea6 TO IDX_F36F794661220EA6');
        $this->addSql('ALTER TABLE art_gallery RENAME INDEX idx_472b783a896dbbde TO IDX_F36F7946896DBBDE');
        $this->addSql('ALTER TABLE art_gallery_label DROP FOREIGN KEY FK_EA3A80CB4E7AF8F');
        $this->addSql('DROP INDEX IDX_EA3A80CB4E7AF8F ON art_gallery_label');
        $this->addSql('ALTER TABLE art_gallery_label DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE art_gallery_label CHANGE gallery_id art_gallery_id INT NOT NULL');
        $this->addSql('ALTER TABLE art_gallery_label ADD CONSTRAINT FK_7D9BFD6731A68AC7 FOREIGN KEY (art_gallery_id) REFERENCES art_gallery (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_7D9BFD6731A68AC7 ON art_gallery_label (art_gallery_id)');
        $this->addSql('ALTER TABLE art_gallery_label ADD PRIMARY KEY (art_gallery_id, label_id)');
        $this->addSql('ALTER TABLE art_gallery_label RENAME INDEX idx_ea3a80cb33b92f39 TO IDX_7D9BFD6733B92F39');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE art_gallery RENAME INDEX uniq_f36f79465e237e06 TO UNIQ_472B783A5E237E06');
        $this->addSql('ALTER TABLE art_gallery RENAME INDEX uniq_f36f7946989d9b62 TO UNIQ_472B783A989D9B62');
        $this->addSql('ALTER TABLE art_gallery RENAME INDEX idx_f36f794661220ea6 TO IDX_472B783A61220EA6');
        $this->addSql('ALTER TABLE art_gallery RENAME INDEX idx_f36f7946896dbbde TO IDX_472B783A896DBBDE');
        $this->addSql('ALTER TABLE art_gallery_label DROP FOREIGN KEY FK_7D9BFD6731A68AC7');
        $this->addSql('DROP INDEX IDX_7D9BFD6731A68AC7 ON art_gallery_label');
        $this->addSql('ALTER TABLE art_gallery_label DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE art_gallery_label CHANGE art_gallery_id gallery_id INT NOT NULL');
        $this->addSql('ALTER TABLE art_gallery_label ADD CONSTRAINT FK_EA3A80CB4E7AF8F FOREIGN KEY (gallery_id) REFERENCES art_gallery (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_EA3A80CB4E7AF8F ON art_gallery_label (gallery_id)');
        $this->addSql('ALTER TABLE art_gallery_label ADD PRIMARY KEY (gallery_id, label_id)');
        $this->addSql('ALTER TABLE art_gallery_label RENAME INDEX idx_7d9bfd6733b92f39 TO IDX_EA3A80CB33B92F39');
    }
}

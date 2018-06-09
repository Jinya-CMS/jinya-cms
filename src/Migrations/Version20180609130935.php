<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180609130935 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE uploading_video_chunk (id INT AUTO_INCREMENT NOT NULL, uploading_video_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', chunk_path VARCHAR(255) NOT NULL, chunk_position INT NOT NULL, INDEX IDX_976C80431C1A5719 (uploading_video_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE uploading_video_chunk ADD CONSTRAINT FK_976C80431C1A5719 FOREIGN KEY (uploading_video_id) REFERENCES uploading_video (id)');
        $this->addSql('ALTER TABLE uploading_video DROP chunk_path');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE uploading_video_chunk');
        $this->addSql('ALTER TABLE uploading_video ADD chunk_path VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');
    }
}

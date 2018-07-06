<?php /** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180625165712 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE video_position (id INT AUTO_INCREMENT NOT NULL, gallery_id INT DEFAULT NULL, video_id INT DEFAULT NULL, youtube_video_id INT DEFAULT NULL, position INT NOT NULL, INDEX IDX_EEAED6494E7AF8F (gallery_id), INDEX IDX_EEAED64929C1004E (video_id), INDEX IDX_EEAED6498E06FC7F (youtube_video_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE video_gallery (id INT AUTO_INCREMENT NOT NULL, creator_id INT DEFAULT NULL, updated_by_id INT DEFAULT NULL, history LONGTEXT NOT NULL, created_at DATETIME NOT NULL, last_updated_at DATETIME NOT NULL, background LONGTEXT DEFAULT NULL, orientation VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, slug VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_A2C691975E237E06 (name), UNIQUE INDEX UNIQ_A2C69197989D9B62 (slug), INDEX IDX_A2C6919761220EA6 (creator_id), INDEX IDX_A2C69197896DBBDE (updated_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE video_position ADD CONSTRAINT FK_EEAED6494E7AF8F FOREIGN KEY (gallery_id) REFERENCES video_gallery (id)');
        $this->addSql('ALTER TABLE video_position ADD CONSTRAINT FK_EEAED64929C1004E FOREIGN KEY (video_id) REFERENCES video (id)');
        $this->addSql('ALTER TABLE video_position ADD CONSTRAINT FK_EEAED6498E06FC7F FOREIGN KEY (youtube_video_id) REFERENCES youtube_video (id)');
        $this->addSql('ALTER TABLE video_gallery ADD CONSTRAINT FK_A2C6919761220EA6 FOREIGN KEY (creator_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE video_gallery ADD CONSTRAINT FK_A2C69197896DBBDE FOREIGN KEY (updated_by_id) REFERENCES users (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE video_position DROP FOREIGN KEY FK_EEAED6494E7AF8F');
        $this->addSql('DROP TABLE video_position');
        $this->addSql('DROP TABLE video_gallery');
    }
}

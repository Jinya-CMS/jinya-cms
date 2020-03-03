<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190923165412 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            'mysql' !== $this->connection->getDatabasePlatform()->getName(),
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('CREATE TABLE theme_gallery (name VARCHAR(255) NOT NULL, theme_id INT NOT NULL, gallery_id INT NOT NULL, INDEX IDX_1E487D1959027487 (theme_id), INDEX IDX_1E487D194E7AF8F (gallery_id), PRIMARY KEY(theme_id, gallery_id, name)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE theme_gallery ADD CONSTRAINT FK_1E487D1959027487 FOREIGN KEY (theme_id) REFERENCES theme (id)');
        $this->addSql('ALTER TABLE theme_gallery ADD CONSTRAINT FK_1E487D194E7AF8F FOREIGN KEY (gallery_id) REFERENCES gallery (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            'mysql' !== $this->connection->getDatabasePlatform()->getName(),
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('DROP TABLE theme_gallery');
    }
}

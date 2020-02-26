<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190920235231 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            'mysql' !== $this->connection->getDatabasePlatform()->getName(),
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE segment ADD gallery_id INT DEFAULT NULL, ADD file_id INT DEFAULT NULL, CHANGE html html VARCHAR(255) DEFAULT NULL, CHANGE action action LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE segment ADD CONSTRAINT FK_1881F5654E7AF8F FOREIGN KEY (gallery_id) REFERENCES gallery (id)');
        $this->addSql('ALTER TABLE segment ADD CONSTRAINT FK_1881F56593CB796C FOREIGN KEY (file_id) REFERENCES file (id)');
        $this->addSql('CREATE INDEX IDX_1881F5654E7AF8F ON segment (gallery_id)');
        $this->addSql('CREATE INDEX IDX_1881F56593CB796C ON segment (file_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            'mysql' !== $this->connection->getDatabasePlatform()->getName(),
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE segment DROP FOREIGN KEY FK_1881F5654E7AF8F');
        $this->addSql('ALTER TABLE segment DROP FOREIGN KEY FK_1881F56593CB796C');
        $this->addSql('DROP INDEX IDX_1881F5654E7AF8F ON segment');
        $this->addSql('DROP INDEX IDX_1881F56593CB796C ON segment');
        $this->addSql('ALTER TABLE segment DROP gallery_id, DROP file_id, CHANGE html html LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE action action VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci');
    }
}

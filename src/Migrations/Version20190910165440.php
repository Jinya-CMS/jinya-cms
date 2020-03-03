<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190910165440 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            'mysql' !== $this->connection->getDatabasePlatform()->getName(),
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, tag VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag_file (tag_id INT NOT NULL, file_id INT NOT NULL, INDEX IDX_629089A6BAD26311 (tag_id), INDEX IDX_629089A693CB796C (file_id), PRIMARY KEY(tag_id, file_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag_folder (tag_id INT NOT NULL, folder_id INT NOT NULL, INDEX IDX_5E340E1BAD26311 (tag_id), INDEX IDX_5E340E1162CB942 (folder_id), PRIMARY KEY(tag_id, folder_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tag_file ADD CONSTRAINT FK_629089A6BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tag_file ADD CONSTRAINT FK_629089A693CB796C FOREIGN KEY (file_id) REFERENCES file (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tag_folder ADD CONSTRAINT FK_5E340E1BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tag_folder ADD CONSTRAINT FK_5E340E1162CB942 FOREIGN KEY (folder_id) REFERENCES folder (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F3610BA96467C');
        $this->addSql('DROP INDEX IDX_8C9F3610BA96467C ON file');
        $this->addSql('ALTER TABLE file DROP galleries_id, DROP tags');
        $this->addSql('ALTER TABLE folder DROP tags');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            'mysql' !== $this->connection->getDatabasePlatform()->getName(),
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE tag_file DROP FOREIGN KEY FK_629089A6BAD26311');
        $this->addSql('ALTER TABLE tag_folder DROP FOREIGN KEY FK_5E340E1BAD26311');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE tag_file');
        $this->addSql('DROP TABLE tag_folder');
        $this->addSql('ALTER TABLE file ADD galleries_id INT DEFAULT NULL, ADD tags LONGTEXT NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:json_array)\'');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F3610BA96467C FOREIGN KEY (galleries_id) REFERENCES gallery_file_position (id)');
        $this->addSql('CREATE INDEX IDX_8C9F3610BA96467C ON file (galleries_id)');
        $this->addSql('ALTER TABLE folder ADD tags LONGTEXT NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:json_array)\'');
    }
}

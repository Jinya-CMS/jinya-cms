<?php

namespace Migrations;

use Jinya\Database\Migration\AbstractMigration;
use PDO;

class FileTags extends AbstractMigration
{
    /**
     * @inheritDoc
     */
    public function getMigrationName(): string
    {
        return 'file-tags';
    }

    /**
     * @inheritDoc
     */
    public function up(PDO $pdo): void
    {
        $pdo->exec(
            <<<'SQL'
create table file_tag (
    id int primary key auto_increment,
    name varchar(255) unique not null,
    color varchar(255) null,
    emoji varchar(255) null
) character set utf8mb4 collate utf8mb4_bin;

create table file_tag_file (
    file_tag_id int not null,
    file_id int not null,
    
    primary key (file_id, file_tag_id),
    constraint FK_file_tag_file_file_tag_id
        foreign key (file_tag_id) references file_tag (id)
            on delete cascade,
    constraint FK_file_tag_file_file_id
        foreign key (file_id) references file (id)
            on delete cascade
) character set utf8mb4 collate utf8mb4_bin;
SQL
        );
    }

    /**
     * @inheritDoc
     */
    public function down(PDO $pdo): void
    {
    }
}

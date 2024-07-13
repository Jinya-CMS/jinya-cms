<?php

namespace Jinya\Cms\Migrations;

use Jinya\Database\Migration\AbstractMigration;
use PDO;

class TG202Folders extends AbstractMigration
{

    public function up(PDO $pdo): void
    {
        $pdo->exec(<<<SQL
create table folder (
    id int primary key auto_increment,
    name text not null,
    parent_id int null,
    
    constraint FK_folder_parent_folder
        foreign key (parent_id) references folder (id)
            on delete cascade
) character set utf8mb4 collate utf8mb4_bin;

alter table file
    add folder_id int null;

alter table file
    add foreign key FK_file_folder (folder_id) references folder (id);
SQL
);
    }

    public function down(PDO $pdo): void
    {
    }
}

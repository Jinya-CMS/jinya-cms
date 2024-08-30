<?php

namespace Jinya\Cms\Migrations;

use Jinya\Database\Migration\AbstractMigration;
use PDO;

class TG202FileUniqueKey extends AbstractMigration
{
    public function up(PDO $pdo): void
    {
        $pdo->exec(
            <<<SQL
alter table file
    add column folder_id_coalesce int generated always as (coalesce(folder_id, -1));

alter table file
    drop key UNIQ_file_name;

alter table file
    add constraint UNIQ_file_name_folder
        unique (name, folder_id_coalesce);
SQL
        );
    }

    public function down(PDO $pdo): void
    {
    }
}

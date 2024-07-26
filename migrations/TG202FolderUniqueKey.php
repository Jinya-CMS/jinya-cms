<?php

namespace Jinya\Cms\Migrations;

use Jinya\Database\Migration\AbstractMigration;
use PDO;

class TG202FolderUniqueKey extends AbstractMigration
{
    public function up(PDO $pdo): void
    {
        $pdo->exec(
            <<<SQL
alter table folder
    add column parent_id_coalesce int generated always as (coalesce(parent_id, -1));

alter table folder
    add constraint UNIQ_folder_name_parent
        unique (name, parent_id_coalesce);
SQL
        );
    }

    public function down(PDO $pdo): void
    {
    }
}

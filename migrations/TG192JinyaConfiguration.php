<?php

namespace Jinya\Cms\Migrations;

use Jinya\Database\Migration\AbstractMigration;
use PDO;

class TG192JinyaConfiguration extends AbstractMigration
{
    /**
     * @inheritDoc
     */
    public function up(PDO $pdo): void
    {
        $pdo->exec(
            <<<SQL
create table jinya_configuration (
    `group` varchar(255) not null,
    `key` varchar(255) not null,
    value varchar(255) not null,
    type int not null,
    
    primary key PK_jinya_configuration (`group`, `key`)
)
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

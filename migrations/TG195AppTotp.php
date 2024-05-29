<?php

namespace Jinya\Cms\Migrations;

use Jinya\Database\Migration\AbstractMigration;
use PDO;

class TG195AppTotp extends AbstractMigration
{

    /**
     * @inheritDoc
     */
    public function up(PDO $pdo): void
    {
        $pdo->exec(<<<SQL
alter table users
    add totp_mode int not null default 0;
alter table users
    add totp_secret varchar(255) null;
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

<?php

namespace Jinya\Cms\Migrations;

use Jinya\Database\Migration\AbstractMigration;
use PDO;

class TG201EmailPreferences extends AbstractMigration
{
    /**
     * @inheritDoc
     */
    public function up(PDO $pdo): void
    {
        $pdo->exec(
            <<<SQL
alter table users
    add login_mail_enabled boolean not null default true;
alter table users
    add new_device_mail_enabled boolean not null default true;
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

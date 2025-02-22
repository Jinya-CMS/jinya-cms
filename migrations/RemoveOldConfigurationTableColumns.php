<?php

namespace Jinya\Cms\Migrations;

use Jinya\Database\Migration\AbstractMigration;
use PDO;

class RemoveOldConfigurationTableColumns extends AbstractMigration
{
    public function __construct()
    {
    }

    /**
     * @inheritDoc
     */
    public function up(PDO $pdo): void
    {
        $pdo->exec(
            <<<SQL
alter table configuration
    drop column if exists invalidate_api_key_after;
alter table configuration
    drop column if exists messaging_center_enabled;
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

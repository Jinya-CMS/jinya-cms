<?php

namespace Jinya\Cms\Migrations;

use Jinya\Database\Migration\AbstractMigration;
use PDO;
use PDOException;

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
        try {
            $pdo->exec(
                <<<SQL
alter table configuration
    drop column invalidate_api_key_after;
alter table configuration
    drop column messaging_center_enabled;
SQL
            );
        } catch (PDOException) {
        }
    }

    /**
     * @inheritDoc
     */
    public function down(PDO $pdo): void
    {
    }
}

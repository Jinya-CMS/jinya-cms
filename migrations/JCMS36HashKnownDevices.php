<?php

namespace Jinya\Cms\Migrations;

use Jinya\Database\Migration\AbstractMigration;
use PDO;

class JCMS36HashKnownDevices extends AbstractMigration
{
    /**
     * @inheritDoc
     */
    public function getMigrationName(): string
    {
        return 'jcms-36-hash-known-devices';
    }

    /**
     * @inheritDoc
     */
    public function up(PDO $pdo): void
    {
        $pdo->exec(
            <<<'SQL'
update known_device
set device_key = sha2(device_key, 512);
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

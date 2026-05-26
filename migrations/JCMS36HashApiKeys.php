<?php

namespace Jinya\Cms\Migrations;

use Jinya\Database\Migration\AbstractMigration;
use PDO;

class JCMS36HashApiKeys extends AbstractMigration
{
    /**
     * @inheritDoc
     */
    public function getMigrationName(): string
    {
        return 'jcms-36-hash-apikeys';
    }

    /**
     * @inheritDoc
     */
    public function up(PDO $pdo): void
    {
        $pdo->exec(
            <<<'SQL'
update api_key
set api_key = sha2(api_key, 512);
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

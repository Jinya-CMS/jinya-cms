<?php

namespace Migrations;

use Jinya\Database\Migration\AbstractMigration;
use PDO;

class BruteForcePrevention extends AbstractMigration
{
    /**
     * @inheritDoc
     */
    public function getMigrationName(): string
    {
        return 'brute-force-prevention';
    }

    /**
     * @inheritDoc
     */
    public function up(PDO $pdo): void
    {
        $pdo->exec(
            <<<'SQL'
alter table users
	add failed_login_attempts int null,
	add login_blocked_until datetime null;
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

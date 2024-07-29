<?php

namespace Jinya\Cms\Migrations;

use Jinya\Database\Migration\AbstractMigration;
use PDO;

class ApiThemeOption extends AbstractMigration
{
    /**
     * @inheritDoc
     */
    public function getMigrationName(): string
    {
        return 'api-theme-option';
    }

    /**
     * @inheritDoc
     */
    public function up(PDO $pdo): void
    {
        $pdo->exec(
            <<<'SQL'
alter table theme
    add column has_api_theme bool not null default false;
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

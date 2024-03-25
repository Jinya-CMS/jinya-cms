<?php

namespace Migrations;

use Jinya\Database\Migration\AbstractMigration;
use PDO;

class CategoryNameNotUnique extends AbstractMigration
{
    /**
     * @inheritDoc
     */
    public function getMigrationName(): string
    {
        return 'category-name-not-unique';
    }

    /**
     * @inheritDoc
     */
    public function up(PDO $pdo): void
    {
        $pdo->exec(
            <<<'SQL'
drop index name on blog_category;
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

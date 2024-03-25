<?php

namespace Migrations;

use Jinya\Database\Migration\AbstractMigration;
use PDO;

class FormItemBoolColumns extends AbstractMigration
{
    /**
     * @inheritDoc
     */
    public function getMigrationName(): string
    {
        return 'form-item-bool-columns';
    }

    /**
     * @inheritDoc
     */
    public function up(PDO $pdo): void
    {
        $pdo->exec(
            <<<'SQL'
alter table form_item 
    modify is_from_address bool default false null;
alter table form_item 
    modify is_subject bool default false null;
alter table form_item 
    modify is_required bool default false null;
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

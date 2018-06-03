<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 25.10.2017
 * Time: 19:24.
 */

namespace Jinya\Components\Database;

interface SchemaToolInterface
{
    /**
     * Migrates the database schema
     */
    public function migrateSchema(): void;

    /**
     * Creates the database schema
     */
    public function createSchema(): void;
}

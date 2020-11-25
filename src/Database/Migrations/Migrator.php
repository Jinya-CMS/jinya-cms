<?php

namespace App\Database\Migrations;

use App\Database\Utils\LoadableEntity;

abstract class Migrator extends LoadableEntity
{
    /**
     * Migrates the installation of Jinya
     */
    public static function migrate(): void
    {
        $sql = self::getSql();
        $connection = $sql->getAdapter()->driver->getConnection();
        $result = $connection->execute("SHOW TABLES LIKE 'migration_state'");
        if ($result->getAffectedRows() === 0) {
            $initialMigration = require __DIR__ . '/initial-migration.php';
            $connection->execute($initialMigration['sql']);
        }

        $migrationsPath = __ROOT__ . '/src/Migrations';
        $files = array_map(
            static fn(string $item) => "$migrationsPath/$item",
            array_filter(scandir($migrationsPath), static fn(string $item) => $item !== '.' && $item !== '..'),
        );

        foreach ($files as $file) {
            /** @noinspection PhpIncludeInspection */
            $migration = require $file;
            $script = $migration['sql'];
            $version = $migration['version'];
            $wasMigrated = $sql
                    ->prepareStatementForSqlObject(
                        $sql->select('version')->from('migration_state')->where(['version' => $version])
                    )
                    ->execute()
                    ->getAffectedRows() > 0;

            if (!$wasMigrated) {
                $connection->execute($script);
                $insert = $sql->insert('migration_state')->columns(['version'])->values([$version]);
                $sql->prepareStatementForSqlObject($insert)->execute();
            }
        }
    }
}
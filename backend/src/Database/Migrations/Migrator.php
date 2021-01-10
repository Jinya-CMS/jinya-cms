<?php

namespace App\Database\Migrations;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\Utils\LoadableEntity;

abstract class Migrator extends LoadableEntity
{
    /**
     * Migrates the installation of Jinya
     *
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    public static function migrate(): void
    {
        $sql = "SHOW TABLES LIKE 'migration_state'";
        $result = self::executeStatement($sql);
        if (count($result) === 0) {
            $initialMigration = require __DIR__ . '/initial-migration.php';
            self::executeStatement($initialMigration['sql']);
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
            $migrateCheckSql = 'SELECT version FROM migration_state WHERE version = :version';
            $result = self::executeStatement($migrateCheckSql, ['version' => $version]);
            $wasMigrated = count($result) > 0;

            if (!$wasMigrated) {
                self::executeStatement($script);
                $insert = 'INSERT INTO migration_state (version) VALUES (:version)';
                self::executeStatement($insert, ['version' => $version]);
            }
        }
    }
}
<?php

namespace App\Database\Migrations;

use App\Database\Analyzer\QueryAnalyzer;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\Utils\LoadableEntity;
use Error;

abstract class Migrator extends LoadableEntity
{
    /**
     * Migrates the installation of Jinya
     *
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    public static function migrate(): int
    {
        $sql = "SHOW TABLES LIKE 'migration_state'";
        $result = self::executeStatement($sql);
        if (0 === count($result)) {
            $initialMigration = require __DIR__ . '/initial-migration.php';
            self::executeSingleMigration($initialMigration['sql']);
        }

        $migrationsPath = __ROOT__ . '/migrations';
        $files = array_map(
            static fn(string $item) => "$migrationsPath/$item",
            array_filter(scandir($migrationsPath), static fn(string $item) => '.' !== $item && '..' !== $item),
        );

        $executedMigrations = 0;
        foreach ($files as $file) {
            $migration = require $file;
            $script = $migration['sql'];
            $version = $migration['version'];
            $migrateCheckSql = 'SELECT version FROM migration_state WHERE version = :version';
            $result = self::executeStatement($migrateCheckSql, ['version' => $version]);
            $wasMigrated = count($result) > 0;

            if (!$wasMigrated) {
                self::executeSingleMigration($script);
                $insert = 'INSERT INTO migration_state (version) VALUES (:version)';
                self::executeStatement($insert, ['version' => $version]);
                ++$executedMigrations;
            }
        }

        return $executedMigrations;
    }

    /**
     * @param string $script
     */
    public static function executeSingleMigration(string $script): void
    {
        $pdo = self::getPdo();
        $queryAnalyzer = new QueryAnalyzer();
        foreach ($queryAnalyzer->getStatements($script) as $statement) {
            $pdo->exec($statement->build());
        }
    }
}

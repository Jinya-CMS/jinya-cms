<?php

namespace App\Database\Migrations;

use App\Database\Analyzer\QueryAnalyzer;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\Utils\LoadableEntity;
use PhpMyAdmin\SqlParser\Statement;

abstract class Migrator extends LoadableEntity
{
    /**
     * @param string $sql
     * @return Statement[]
     */
    private static function getStatements(string $sql): array
    {
        $queryAnalyzer = new QueryAnalyzer();
        return $queryAnalyzer->getStatements($sql);
    }

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
            $stmts = self::getStatements($initialMigration['sql']);
            foreach ($stmts as $stmt) {
                self::executeStatement($stmt->build());
            }
        }

        $migrationsPath = __ROOT__ . '/migrations';
        $files = array_map(
            static fn(string $item) => "$migrationsPath/$item",
            array_filter(scandir($migrationsPath), static fn(string $item) => '.' !== $item && '..' !== $item),
        );

        $executedMigrations = 0;
        foreach ($files as $file) {
            /** @noinspection PhpIncludeInspection */
            $migration = require $file;
            $script = $migration['sql'];
            $version = $migration['version'];
            $migrateCheckSql = 'SELECT version FROM migration_state WHERE version = :version';
            $result = self::executeStatement($migrateCheckSql, ['version' => $version]);
            $wasMigrated = count($result) > 0;

            if (!$wasMigrated) {
                $stmts = self::getStatements($script);
                foreach ($stmts as $stmt) {
                    self::executeStatement($stmt->build());
                }
                $insert = 'INSERT INTO migration_state (version) VALUES (:version)';
                self::executeStatement($insert, ['version' => $version]);
                ++$executedMigrations;
            }
        }

        return $executedMigrations;
    }
}

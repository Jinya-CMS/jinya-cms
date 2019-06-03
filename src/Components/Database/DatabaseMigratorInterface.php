<?php

namespace Jinya\Components\Database;

use Doctrine\DBAL\DBALException;

interface DatabaseMigratorInterface
{
    /**
     * Migrate the database to the latest version
     */
    public function migrate(): void;

    /**
     * Get the latest migration version
     *
     * @return string
     */
    public function getLatestMigrationVersion(): string;

    /**
     * Get all available migrations
     *
     * @return array
     */
    public function getAllMigrations(): array;

    /**
     * Get a specific migration by its version
     *
     * @param string $version
     * @return string
     */
    public function getMigrationByVersion(string $version): string;

    /**
     * Sets the given version as the current migration version
     *
     * @param string $version
     * @throws DBALException
     */
    public function setMigrationVersion(string $version): void;

    /**
     * Activates all migrations
     * @throws DBALException
     */
    public function activateAllMigrations(): void;
}

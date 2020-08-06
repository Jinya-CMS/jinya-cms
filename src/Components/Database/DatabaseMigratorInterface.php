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
     */
    public function getLatestMigrationVersion(): string;

    /**
     * Sets the given version as the current migration version
     *
     * @throws DBALException
     */
    public function setMigrationVersion(string $version): void;

    /**
     * Activates all migrations
     * @throws DBALException
     */
    public function activateAllMigrations(): void;
}

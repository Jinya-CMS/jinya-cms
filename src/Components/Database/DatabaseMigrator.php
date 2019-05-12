<?php

namespace Jinya\Components\Database;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Migrations\Configuration\Configuration;
use Doctrine\DBAL\Migrations\Migration;
use Doctrine\DBAL\Migrations\MigrationException;

class DatabaseMigrator implements DatabaseMigratorInterface
{

    /** @var Connection */
    private $connection;

    /** @var string */
    private $tableName;

    /** @var string */
    private $namespace;

    /** @var string */
    private $kernelRootDir;

    /**
     * DatabaseMigrator constructor.
     * @param Connection $connection
     * @param string $tableName
     * @param string $namespace
     * @param string $kernelRootDir
     */
    public function __construct(Connection $connection, string $tableName, string $namespace, string $kernelRootDir)
    {
        $this->connection = $connection;
        $this->tableName = $tableName;
        $this->namespace = $namespace;
        $this->kernelRootDir = $kernelRootDir;
        try {
            $this->createTableIfNotExists();
        } catch (DBALException $e) {
        }
    }

    /**
     * @throws DBALException
     */
    private function createTableIfNotExists(): void
    {
        if ($this->connection->getDatabasePlatform()->getName() !== 'mysql') {
            return;
        }

        $tableExists = $this->connection->fetchColumn('SELECT table_name FROM information_schema.tables WHERE table_schema = :databaseName AND table_name = :tableName',
            [
                'tableName' => $this->tableName,
                'databaseName' => $this->connection->getDatabase(),
            ]);

        if (!$tableExists) {
            $tableName = $this->tableName;
            $this->connection->exec("CREATE TABLE IF NOT EXISTS $tableName (version VARCHAR(255) NOT NULL)");
        }
    }

    /**
     * Migrate the database to the latest version
     * @throws MigrationException
     */
    public function migrate(): void
    {
        $migration = $this->getMigrationProvider();
        $migration->migrate();
    }

    /**
     * @return Migration
     */
    private function getMigrationProvider(): Migration
    {
        $config = new Configuration($this->connection);
        $config->setMigrationsTableName($this->tableName);
        $config->setMigrationsNamespace($this->namespace);
        $config->setMigrationsDirectory($this->kernelRootDir . '/Migrations');
        $config->registerMigrationsFromDirectory($config->getMigrationsDirectory());

        return new Migration($config);
    }

    /**
     * Get the latest migration
     *
     * @return string
     */
    public function getLatestMigrationVersion(): string
    {
        $versions = $this->getMigrationProvider()->getSql();
        end($versions);

        return key($versions);
    }

    /**
     * Get a specific migration by its version
     *
     * @param string $version
     * @return string
     */
    public function getMigrationByVersion(string $version): string
    {
        return $this->getMigrationProvider()->getSql()[$version];
    }

    /**
     * Sets the given version as the current migration version
     *
     * @param string $version
     * @throws DBALException
     */
    public function setMigrationVersion(string $version): void
    {
        $this->connection->beginTransaction();
        try {
            $this->connection->insert($this->tableName, ['version' => $version]);
            $this->connection->commit();
        } catch (DBALException $exception) {
            $this->connection->rollBack();
            throw $exception;
        }
    }

    /**
     * Activates all migrations
     * @throws DBALException
     */
    public function activateAllMigrations(): void
    {
        $versions = $this->getAllMigrations();
        $this->connection->beginTransaction();
        try {
            foreach ($versions as $version => $item) {
                $this->connection->insert($this->tableName, ['version' => $version]);
            }
            $this->connection->commit();
        } catch (DBALException $exception) {
            $this->connection->rollBack();
            throw $exception;
        }
    }

    /**
     * Get all available migrations
     *
     * @return array
     */
    public function getAllMigrations(): array
    {
        return $this->getMigrationProvider()->getSql();
    }
}
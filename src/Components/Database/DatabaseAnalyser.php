<?php

namespace Jinya\Components\Database;

use Doctrine\DBAL\Connection;

class DatabaseAnalyser implements DatabaseAnalyserInterface
{
    private Connection $connection;

    /**
     * DatabaseAnalyser constructor.
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * {@inheritdoc}
     */
    public function getGlobalVariables(): array
    {
        return $this->connection->fetchAll('SHOW GLOBAL VARIABLES');
    }

    /**
     * {@inheritdoc}
     */
    public function getLocalVariables(): array
    {
        return $this->connection->fetchAll('SHOW VARIABLES');
    }

    /**
     * {@inheritdoc}
     */
    public function getVersion(): string
    {
        return $this->connection->fetchColumn('SELECT VERSION()');
    }

    /**
     * {@inheritdoc}
     */
    public function getServerType(): string
    {
        return $this->connection->fetchArray('SHOW GLOBAL VARIABLES WHERE Variable_name = \'version_comment\'')[1];
    }
}

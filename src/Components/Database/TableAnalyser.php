<?php

namespace Jinya\Components\Database;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;

class TableAnalyser implements TableAnalyserInterface
{
    private Connection $connection;

    /**
     * TableAnalyser constructor.
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @inheritDoc
     * @throws DBALException
     */
    public function getTableMetadata(string $table): ?TableMetadata
    {
        $tables = $this->getTables();
        if (in_array($table, $tables, true)) {
            $metadata = new TableMetadata();
            $fields = [];
            $data = $this->connection->fetchAll("EXPLAIN $table");
            $checksum = $this->connection->fetchColumn("CHECKSUM TABLE $table");
            $metadata->setChecksum($checksum ?? '');
            foreach ($data as $datum) {
                $field = new TableFieldMetadata();
                $field->setDefault($datum['default']);
                $field->setExtra($datum['extra']);
                $field->setField($datum['field']);
                $field->setKey($datum['key']);
                $field->setNullable($datum['nullable']);
                $field->setType($datum['type']);

                $fields[] = $field;
            }
            $metadata->setFields($fields);

            return $metadata;
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function getTables(): array
    {
        return array_values($this->connection->fetchAll('SHOW TABLES'));
    }
}
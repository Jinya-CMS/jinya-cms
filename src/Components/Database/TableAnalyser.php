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
     * {@inheritdoc}
     * @throws DBALException
     */
    public function getTableMetadata(string $table): ?TableMetadata
    {
        $tables = $this->getTables();
        if (array_filter($tables, fn ($key) => $table === array_values($key)[0])) {
            $metadata = new TableMetadata();
            $fields = [];
            $data = $this->connection->fetchAll("EXPLAIN $table");
            $checksum = $this->connection->fetchAll("CHECKSUM TABLE $table")[0]['Checksum'];
            $metadata->setChecksum($checksum ?? '');
            foreach ($data as $datum) {
                $field = new TableFieldMetadata();
                $field->setDefault($datum['Default']);
                $field->setExtra($datum['Extra']);
                $field->setField($datum['Field']);
                $field->setKey($datum['Key']);
                $field->setNullable('no' !== strtolower($datum['Null']));
                $field->setType($datum['Type']);

                $fields[] = $field;
            }
            $metadata->setFields($fields);

            return $metadata;
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getTables(): array
    {
        return array_values($this->connection->fetchAll('SHOW TABLES'));
    }
}

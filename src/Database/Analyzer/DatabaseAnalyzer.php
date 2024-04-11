<?php

namespace Jinya\Cms\Database\Analyzer;

use Aura\SqlQuery\Common\SelectInterface;
use Error;
use Exception;
use JetBrains\PhpStorm\ArrayShape;
use Jinya\Database\Entity;
use LogicException;
use PDOException;

/**
 * This class analyzes the database and allows to retrieve database information
 */
class DatabaseAnalyzer
{
    /**
     * Gets all tables currently present in the active database
     *
     * @return array<int|string, array<string, mixed|int|string>> An array of the tables and their fields
     * @throws Exception
     */
    public static function getTables(): array
    {
        $tables = self::executeSqlString('SHOW TABLES');
        $result = [];
        if (!is_array($tables)) {
            throw new LogicException('Query must return an array');
        }
        foreach ($tables as $table) {
            $tableName = $table[array_keys($table)[0]];
            $result[$tableName]['structure'] = self::executeSqlString("EXPLAIN $tableName");

            $query = Entity::getQueryBuilder()
                ->newSelect()
                ->from($tableName)
                ->cols(['COUNT(*) AS count']);
            $result[$tableName]['entryCount'] = self::fetchInt($query, 'count');

            try {
                $query = Entity::getQueryBuilder()
                    ->newSelect()
                    ->from('information_schema.TABLES')
                    ->cols(['ROUND((DATA_LENGTH + INDEX_LENGTH)) AS bytes'])
                    ->where('TABLE_NAME = :tableName', ['tableName' => $tableName]);
                $result[$tableName]['size'] = self::fetchInt($query, 'bytes');

                $query = Entity::getQueryBuilder()
                    ->newSelect()
                    ->from('information_schema.TABLES')
                    ->cols(['ENGINE AS engine'])
                    ->where('TABLE_NAME = :tableName', ['tableName' => $tableName]);
                $result[$tableName]['engine'] = self::fetchString($query, 'engine');

                $query = Entity::getQueryBuilder()
                    ->newSelect()
                    ->from('information_schema.TABLE_CONSTRAINTS tc')
                    ->cols([
                        'kcu.CONSTRAINT_NAME AS CONSTRAINT_NAME',
                        'kcu.REFERENCED_TABLE_NAME AS REFERENCED_TABLE_NAME',
                        'kcu.REFERENCED_COLUMN_NAME AS REFERENCED_COLUMN_NAME',
                        'kcu.POSITION_IN_UNIQUE_CONSTRAINT AS POSITION_IN_UNIQUE_CONSTRAINT',
                        'tc.CONSTRAINT_TYPE AS CONSTRAINT_TYPE',
                        'rc.DELETE_RULE AS DELETE_RULE',
                        'rc.UPDATE_RULE AS UPDATE_RULE',
                        'kcu.COLUMN_NAME AS COLUMN_NAM'
                    ])
                    ->innerJoin(
                        'information_schema.KEY_COLUMN_USAGE kcu',
                        'kcu.CONSTRAINT_NAME = tc.CONSTRAINT_NAME and tc.TABLE_NAME = kcu.TABLE_NAME'
                    )
                    ->leftJoin(
                        'information_schema.REFERENTIAL_CONSTRAINTS rc',
                        'rc.CONSTRAINT_NAME = kcu.CONSTRAINT_NAME'
                    )
                    ->where('tc.TABLE_NAME = :tableName', ['tableName' => $tableName]);
                $result[$tableName]['constraints'] = Entity::executeQuery($query);
                $result[$tableName]['indexes'] = self::executeSqlString("SHOW INDEXES FROM $tableName");
            } catch (Error) {
                $result[$tableName]['size'] = 0;
                $result[$tableName]['engine'] = '';
            }
        }

        return $result;
    }

    /**
     * @param string $sql
     * @return array<array-key, mixed>|int
     */
    private static function executeSqlString(string $sql): array|int
    {
        $pdo = Entity::getPdo();
        $stmt = $pdo->query($sql);
        if ($stmt && $stmt->columnCount() > 0) {
            return $stmt->fetchAll();
        }

        return $stmt ? $stmt->rowCount() : 0;
    }

    private static function fetchInt(SelectInterface $query, string $column): int
    {
        $result = Entity::executeQuery($query);
        if (is_array($result)) {
            /** @var int $result */
            $result = $result[0][$column];
        } else {
            $result = 0;
        }

        return $result;
    }

    private static function fetchString(SelectInterface $query, string $column): string
    {
        $result = Entity::executeQuery($query);
        if (is_array($result)) {
            /** @var string $result */
            $result = $result[0][$column];
        } else {
            $result = '';
        }

        return $result;
    }

    /**
     * Gets the information for the current database server. This information includes the version and the OS and architecture the server was compiled on
     *
     * @return array<string, string> An array containing the version, the comment, the machine and OS the database was compiled on
     */
    #[ArrayShape(['version' => 'string', 'comment' => 'string', 'compileMachine' => 'string', 'compileOs' => 'string'])]
    public static function getServerInfo(): array
    {
        $variables = self::getVariables(VariablesType::Global);

        return [
            'version' => $variables['version'],
            'comment' => $variables['version_comment'],
            'compileMachine' => $variables['version_compile_machine'],
            'compileOs' => $variables['version_compile_os'],
        ];
    }

    /**
     * Gets the variables of the database server for one of the variable types
     *
     * @param VariablesType $type
     * @return array<int|string, mixed>
     */
    public static function getVariables(VariablesType $type): array
    {
        $stringType = match ($type) {
            VariablesType::Global => 'GLOBAL',
            VariablesType::Local => 'LOCAL',
            VariablesType::Session => 'SESSION',
        };

        try {
            $variables = self::executeSqlString("SHOW $stringType VARIABLES");
            if (!is_array($variables)) {
                throw new LogicException('Query must return an array');
            }
        } catch (PDOException) {
            $variables = [];
        }

        $returnVal = [];
        foreach ($variables as $variable) {
            $returnVal[$variable['Variable_name']] = $variable['Value'];
        }

        return $returnVal;
    }
}

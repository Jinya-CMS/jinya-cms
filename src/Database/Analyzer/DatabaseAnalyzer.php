<?php

namespace App\Database\Analyzer;

use App\Database\Utils\LoadableEntity;
use Error;
use JetBrains\PhpStorm\ArrayShape;
use LogicException;

/**
 * This class analyzes the database and allows to retrieve several database information
 */
class DatabaseAnalyzer
{

    /**
     * Gets all tables currently present in the active database
     *
     * @return array<int|string, array<string, mixed|int|string>> An array of the tables and their fields
     */
    public static function getTables(): array
    {
        $tables = LoadableEntity::executeSqlString('SHOW TABLES');
        $result = [];
        if (!is_array($tables)) {
            throw new LogicException('Query must return array');
        }
        foreach ($tables as $table) {
            $tableName = $table[array_keys($table)[0]];
            $result[$tableName]['structure'] = LoadableEntity::executeSqlString("EXPLAIN $tableName");
            $result[$tableName]['entryCount'] = LoadableEntity::fetchColumn("SELECT COUNT(*) FROM $tableName");
            try {
                $result[$tableName]['size'] = LoadableEntity::fetchColumn("SELECT ROUND((DATA_LENGTH + INDEX_LENGTH)) AS bytes FROM information_schema.TABLES WHERE TABLE_NAME = '$tableName'");
                $result[$tableName]['engine'] = LoadableEntity::fetchColumn("SELECT ENGINE FROM information_schema.TABLES WHERE TABLE_NAME = '$tableName'");
                $result[$tableName]['constraints'] = LoadableEntity::executeSqlString("select kcu.CONSTRAINT_NAME, kcu.REFERENCED_TABLE_NAME, kcu.REFERENCED_COLUMN_NAME, kcu.POSITION_IN_UNIQUE_CONSTRAINT, tc.CONSTRAINT_TYPE, rc.DELETE_RULE, rc.UPDATE_RULE, kcu.COLUMN_NAME from information_schema.TABLE_CONSTRAINTS tc inner join information_schema.KEY_COLUMN_USAGE kcu on kcu.CONSTRAINT_NAME = tc.CONSTRAINT_NAME and tc.TABLE_NAME = kcu.TABLE_NAME left join information_schema.REFERENTIAL_CONSTRAINTS rc on rc.CONSTRAINT_NAME = kcu.CONSTRAINT_NAME WHERE tc.TABLE_NAME = '$tableName'");
                $result[$tableName]['indexes'] = LoadableEntity::executeSqlString("SHOW INDEXES FROM $tableName");
            } catch (Error) {
                $result[$tableName]['size'] = 0;
                $result[$tableName]['engine'] = '';
            }
        }

        return $result;
    }

    /**
     * Gets the information for the current database server. This information includes the version and the OS and architecture the server was compiled on
     *
     * @return array<string, string> An array containing the version, the comment, the compile machine and the compile OS
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
        $variables = LoadableEntity::executeSqlString("SHOW $stringType VARIABLES");
        if (!is_array($variables)) {
            throw new LogicException('Query must return array');
        }

        $returnVal = [];
        foreach ($variables as $variable) {
            $returnVal[$variable['Variable_name']] = $variable['Value'];
        }

        return $returnVal;
    }
}

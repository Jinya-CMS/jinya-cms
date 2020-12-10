<?php

namespace App\Database\Analyzer;

use App\Database\Utils\LoadableEntity;
use JetBrains\PhpStorm\ArrayShape;

class DatabaseAnalyzer
{
    public const GLOBAL_VARIABLES = 'GLOBAL';
    public const LOCAL_VARIABLES = 'LOCAL';
    public const SESSION_VARIABLES = 'SESSION';

    public static function getTables(): array
    {
        $tables = LoadableEntity::executeSqlString('SHOW TABLES');
        return array_map(
            static fn(array $table) => LoadableEntity::executeSqlString("EXPLAIN ${table['Tables_in_jinya']}"),
            $tables,
        );
    }

    #[ArrayShape(['version' => 'string', 'comment' => 'string', 'compileMachine' => 'string', 'compileOs' => 'string'])]
    public static function getServerInfo(): array
    {
        $variables = self::getVariables(true);

        return [
            'version' => $variables['version'],
            'comment' => $variables['version_comment'],
            'compileMachine' => $variables['version_compile_machine'],
            'compileOs' => $variables['version_compile_os'],
        ];
    }

    public static function getVariables(string $type): array
    {
        $variables = LoadableEntity::executeSqlString("SHOW $type VARIABLES");

        $returnVal = [];
        foreach ($variables as $variable) {
            $returnVal[$variable['Variable_name']] = $variable['Value'];
        }

        return $returnVal;
    }
}
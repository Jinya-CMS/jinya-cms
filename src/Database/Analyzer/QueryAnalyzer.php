<?php

namespace App\Database\Analyzer;

use PhpMyAdmin\SqlParser\Parser;
use PhpMyAdmin\SqlParser\Statement;
use PhpMyAdmin\SqlParser\Utils\Query;

/**
 * Helper class to wrap phpmyadmin SQL parser
 */
class QueryAnalyzer
{
    /**
     * Gets the query type
     */
    public function getQueryType(Statement $statement): string
    {
        return Query::getFlags($statement)['querytype'];
    }

    /**
     * Gets the permitted statements from the query
     *
     * @return Statement[]
     */
    public function getStatements(string $query): array
    {
        return (new Parser($query))->statements;
    }
}

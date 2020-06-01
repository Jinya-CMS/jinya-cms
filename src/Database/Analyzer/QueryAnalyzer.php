<?php

namespace App\Database\Analyzer;

use PhpMyAdmin\SqlParser\Parser;
use PhpMyAdmin\SqlParser\Statement;
use PhpMyAdmin\SqlParser\Utils\Query;

class QueryAnalyzer
{
    /**
     * Gets the query type
     *
     * @param Statement $statement
     * @return string
     */
    public function getQueryType(Statement $statement): string
    {
        return Query::getFlags($statement)['querytype'];
    }

    /**
     * Gets the permitted statements from the query
     *
     * @param string $query
     * @return Statement[]
     */
    public function getStatements(string $query): array
    {
        return (new Parser($query))->statements;
    }
}
<?php

namespace Jinya\Components\Database;

use Iterator;
use PhpMyAdmin\SqlParser\Statement;

interface QueryAnalyserInterface
{
    /**
     * Gets the permitted statements from the query
     *
     * @param string $query
     * @return Statement[]
     */
    public function getStatements(string $query): array;

    /**
     * Gets the query type
     *
     * @param Statement $statement
     * @return string
     */
    public function getQueryType(Statement $statement): string;
}

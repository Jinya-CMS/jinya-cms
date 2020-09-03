<?php

namespace Jinya\Components\Database;

use PhpMyAdmin\SqlParser\Statement;

interface QueryAnalyserInterface
{
    /**
     * Gets the permitted statements from the query
     *
     * @return Statement[]
     */
    public function getStatements(string $query): array;

    /**
     * Gets the query type
     */
    public function getQueryType(Statement $statement): string;
}

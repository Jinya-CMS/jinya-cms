<?php

namespace Jinya\Components\Database;

use PhpMyAdmin\SqlParser\Parser;
use PhpMyAdmin\SqlParser\Statement;
use PhpMyAdmin\SqlParser\Utils\Query;

class QueryAnalyser implements QueryAnalyserInterface
{
    /**
     * {@inheritdoc}
     */
    public function getQueryType(Statement $statement): string
    {
        return Query::getFlags($statement)['querytype'];
    }

    /**
     * {@inheritdoc}
     */
    public function getStatements(string $query): array
    {
        return (new Parser($query))->statements;
    }
}

<?php

namespace Jinya\Components\Database;

use ArrayIterator;
use Doctrine\Migrations\Version\State;
use Iterator;
use PhpMyAdmin\SqlParser\Parser;
use PhpMyAdmin\SqlParser\Statement;
use PhpMyAdmin\SqlParser\Utils\Query;

class QueryAnalyser implements QueryAnalyserInterface
{
    /**
     * @inheritDoc
     */
    public function getQueryType(Statement $statement): string
    {
        return Query::getFlags($statement)['querytype'];
    }

    /**
     * @inheritDoc
     */
    public function getStatements(string $query): Iterator
    {
        $statements = (new Parser($query))->statements;
        foreach ($statements as $statement) {
            $flags = Query::getFlags($statement);
            if (in_array($flags['querytype'], ['SELECT', 'DELETE', 'INSERT', 'UPDATE', 'EXPLAIN'])) {
                yield $statement;
            }
        }
        return new ArrayIterator();
    }
}
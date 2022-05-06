<?php

namespace App\Web\Actions\Database;

use App\Database\Analyzer\QueryAnalyzer;
use App\Database\Utils\LoadableEntity;
use App\Web\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;

/**
 *
 */
class ExecuteQueryAction extends Action
{
    /**
     * @return Response
     */
    protected function action(): Response
    {
        $query = $this->body['query'];

        $queryAnalyzer = new QueryAnalyzer();

        $statements = $queryAnalyzer->getStatements($query);

        $result = [];
        foreach ($statements as $statement) {
            $builtStatement = $statement->build();
            $result[] = match ($queryAnalyzer->getQueryType($statement)) {
                'UPDATE', 'DELETE', 'INSERT', 'SELECT', 'EXPLAIN', 'CHECK', 'CHECKSUM', 'ANALYSE', 'SHOW' => [
                    'statement' => $builtStatement,
                    'result' => LoadableEntity::executeSqlString($builtStatement),
                ],
                default => [
                    'statement' => $builtStatement,
                    'result' => 'Query not allowed',
                ],
            };
        }

        return $this->respond($result);
    }
}

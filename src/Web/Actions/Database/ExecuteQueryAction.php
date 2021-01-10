<?php

namespace App\Web\Actions\Database;

use App\Database\Analyzer\QueryAnalyzer;
use App\Database\Utils\LoadableEntity;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Attributes\RequiredFields;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/maintenance/database/query', JinyaAction::POST)]
#[Authenticated(role: Authenticated::ADMIN)]
#[RequiredFields(['query'])]
class ExecuteQueryAction extends Action
{
    /**
     * @throws JsonException
     */
    protected function action(): Response
    {
        $query = $this->request->getParsedBody()['query'];

        $queryAnalyzer = new QueryAnalyzer();

        $statements = $queryAnalyzer->getStatements($query);

        $result = [];
        foreach ($statements as $statement) {
            $builtStatement = $statement->build();
            switch ($queryAnalyzer->getQueryType($statement)) {
                case 'UPDATE':
                case 'DELETE':
                case 'INSERT':
                case 'SELECT':
                case 'EXPLAIN':
                case 'CHECK':
                case 'CHECKSUM':
                case 'ANALYSE':
                case 'SHOW':
                    $result[] = [
                        'statement' => $builtStatement,
                        'result' => LoadableEntity::executeSqlString($builtStatement),
                    ];
                    break;
                default:
                    $result[] = [
                        'statement' => $builtStatement,
                        'result' => 'Query not allowed',
                    ];

                    break;
            }
        }

        return $this->respond($result);
    }
}

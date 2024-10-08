<?php

namespace Jinya\Cms\Web\Controllers;

use Exception;
use Jinya\Cms\Database\Analyzer\DatabaseAnalyzer;
use Jinya\Cms\Database\Analyzer\QueryAnalyzer;
use Jinya\Cms\Database\Analyzer\VariablesType;
use Jinya\Cms\Web\Middleware\AuthorizationMiddleware;
use Jinya\Cms\Web\Middleware\CheckRequiredFieldsMiddleware;
use Jinya\Database\Entity;
use Jinya\Router\Attributes\Controller;
use Jinya\Router\Attributes\HttpMethod;
use Jinya\Router\Attributes\Middlewares;
use Jinya\Router\Attributes\Route;
use JsonException;
use PDOException;
use Psr\Http\Message\ResponseInterface;

#[Controller]
#[Middlewares(new AuthorizationMiddleware(ROLE_ADMIN))]
class DatabaseController extends BaseController
{
    /**
     * @throws JsonException
     */
    #[Route(HttpMethod::POST, 'api/maintenance/database/query')]
    #[Middlewares(new CheckRequiredFieldsMiddleware(['query']))]
    public function queryDatabase(): ResponseInterface
    {
        $query = $this->body['query'];

        $queryAnalyzer = new QueryAnalyzer();

        $statements = $queryAnalyzer->getStatements($query);

        $result = [];
        foreach ($statements as $statement) {
            $builtStatement = $statement->build();
            try {
                $result[] = match ($queryAnalyzer->getQueryType($statement)) {
                    'UPDATE', 'DELETE', 'INSERT', 'SELECT', 'EXPLAIN', 'SHOW' => [
                        'statement' => $builtStatement,
                        'result' => $this->executeSqlString($builtStatement),
                    ],
                    default => [
                        'statement' => $builtStatement,
                        'result' => "Query hasn't been allowed",
                    ],
                };
            } catch (PDOException $exception) {
                $result[] = [
                    'statement' => $builtStatement,
                    'result' => 'Query is invalid: ' . $exception->getMessage(),
                ];
            }
        }

        return $this->json($result);
    }

    /**
     * @param string $sql
     * @return array<array-key, mixed>|int
     */
    private function executeSqlString(string $sql): array|int
    {
        $pdo = Entity::getPdo();
        $stmt = $pdo->query($sql);
        if ($stmt && $stmt->columnCount() > 0) {
            return $stmt->fetchAll();
        }

        return $stmt ? $stmt->rowCount() : 0;
    }

    /**
     * @throws JsonException
     * @throws Exception
     */
    #[Route(HttpMethod::GET, 'api/maintenance/database/analyze')]
    public function analyzeDatabase(): ResponseInterface
    {
        $tables = DatabaseAnalyzer::getTables();
        $server = DatabaseAnalyzer::getServerInfo();
        $localVariables = DatabaseAnalyzer::getVariables(VariablesType::Local);
        $globalVariables = DatabaseAnalyzer::getVariables(VariablesType::Global);
        $sessionVariables = DatabaseAnalyzer::getVariables(VariablesType::Session);

        return $this->json([
            'tables' => $tables,
            'server' => $server,
            'variables' => [
                'local' => $localVariables,
                'session' => $sessionVariables,
                'global' => $globalVariables,
            ],
        ]);
    }
}

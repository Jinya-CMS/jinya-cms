<?php

namespace Jinya\Controller\Api\Maintenance;

use Doctrine\DBAL\Connection;
use Jinya\Components\Database\QueryAnalyserInterface;
use Jinya\Framework\BaseApiController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

class DatabaseController extends BaseApiController
{
    /**
     * @Route("api/maintenance/database/query", methods={"POST"}, name="api_maintenance_database_query_execute")
     * @IsGranted("ROLE_SUPER_ADMIN")
     *
     * @param QueryAnalyserInterface $queryAnalyser
     * @param Connection $connection
     * @return Response
     */
    public function executeQuery(QueryAnalyserInterface $queryAnalyser, Connection $connection): Response
    {
        [$data, $status] = $this->tryExecute(function () use ($queryAnalyser, $connection) {
            $query = $this->getValue('query');
            $statements = $queryAnalyser->getStatements($query);

            $connection->beginTransaction();

            try {
                $result = [];
                foreach ($statements as $statement) {
                    $builtStatement = $statement->build();
                    switch ($queryAnalyser->getQueryType($statement)) {
                        case 'UPDATE':
                        case 'DELETE':
                        case 'INSERT':
                            $result[] = [
                                'statement' => $builtStatement,
                                'result' => $connection->exec($builtStatement),
                            ];

                            break;
                        case 'SELECT':
                            $result[] = [
                                'statement' => $builtStatement,
                                'result' => $connection->fetchAll($builtStatement),
                            ];

                            break;
                        default:
                            break;
                    }
                }

                return $result;
            } catch (Throwable $exception) {
                $connection->rollBack();

                throw $exception;
            } finally {
                $connection->commit();
            }

            return 0;
        });

        return $this->json($data, $status);
    }
}

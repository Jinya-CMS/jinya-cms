<?php

namespace Jinya\Controller\Api\Maintenance;

use Doctrine\DBAL\Connection;
use Jinya\Components\Database\QueryAnalyserInterface;
use Jinya\Framework\BaseApiController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

class DatabaseController extends BaseApiController
{
    /**
     * @Route("api/maintenance/database/query", methods={"POST"}, name="api_maintenance_database_query_execute")
     * @IsGranted("ROLE_SUPER_ADMIN")
     *
     * @param QueryAnalyserInterface $queryAnalyser
     * @param Connection $connection
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function executeQuery(
        QueryAnalyserInterface $queryAnalyser,
        Connection $connection,
        TranslatorInterface $translator
    ): Response {
        [$data, $status] = $this->tryExecute(function () use ($queryAnalyser, $connection, $translator) {
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
                        case 'EXPLAIN':
                        case 'CHECK':
                        case 'CHECKSUM':
                        case 'ANALYSE':
                        case 'SHOW':
                            $result[] = [
                                'statement' => $builtStatement,
                                'result' => $connection->fetchAll($builtStatement),
                            ];

                            break;
                        default:
                            $result[] = [
                                'statement' => $builtStatement,
                                'result' => $translator->trans('maintenance.database.tool.result.not_executed'),
                            ];

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
        });

        return $this->json($data, $status);
    }
}

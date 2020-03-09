<?php

namespace Jinya\Controller\Api\Maintenance;

use Doctrine\DBAL\Connection;
use Jinya\Components\Database\DatabaseAnalyserInterface;
use Jinya\Components\Database\QueryAnalyserInterface;
use Jinya\Components\Database\TableAnalyserInterface;
use Jinya\Framework\BaseApiController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

class DatabaseController extends BaseApiController
{
    /**
     * @Route("api/maintenance/database/table", methods={"GET"}, name="api_maintenance_database_table")
     * @IsGranted("ROLE_SUPER_ADMIN")
     *
     * @param TableAnalyserInterface $tableAnalyser
     * @return Response
     */
    public function getTableInfo(TableAnalyserInterface $tableAnalyser): Response
    {
        [$data, $status] = $this->tryExecute(static function () use ($tableAnalyser) {
            $tables = $tableAnalyser->getTables();
            $meta = [];

            foreach ($tables as $table) {
                $meta[] = $tableAnalyser->getTableMetadata($table);
            }

            return $meta;
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("api/maintenance/database", methods={"GET"}, name="api_maintenance_database")
     * @IsGranted("ROLE_SUPER_ADMIN")
     *
     * @param DatabaseAnalyserInterface $databaseAnalyser
     * @return Response
     */
    public function getDatabaseInfo(DatabaseAnalyserInterface $databaseAnalyser): Response
    {
        [$data, $status] = $this->tryExecute(static function () use ($databaseAnalyser) {
            $localVariables = $databaseAnalyser->getLocalVariables();
            $globalVariables = $databaseAnalyser->getGlobalVariables();
            $serverType = $databaseAnalyser->getServerType();
            $version = $databaseAnalyser->getVersion();

            return [
                'variables' => [
                    'local' => $localVariables,
                    'global' => $globalVariables,
                ],
                'server' => [
                    'version' => $version,
                    'type' => $serverType,
                ],
            ];
        });

        return $this->json($data, $status);
    }

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

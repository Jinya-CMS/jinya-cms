<?php

namespace App\Web\Actions\Database;

use App\Database\Analyzer\DatabaseAnalyzer;
use App\Database\Analyzer\VariablesType;
use App\Web\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Action to analyse the database
 */
class DatabaseAnalyzerAction extends Action
{
    /**
     * Analyses the tables, server details and variables of the database
     *
     * @return Response
     */
    protected function action(): Response
    {
        $tables = DatabaseAnalyzer::getTables();
        $server = DatabaseAnalyzer::getServerInfo();
        $localVariables = DatabaseAnalyzer::getVariables(VariablesType::Local);
        $globalVariables = DatabaseAnalyzer::getVariables(VariablesType::Global);
        $sessionVariables = DatabaseAnalyzer::getVariables(VariablesType::Session);

        return $this->respond(
            [
                'tables' => $tables,
                'server' => $server,
                'variables' => [
                    'local' => $localVariables,
                    'session' => $sessionVariables,
                    'global' => $globalVariables,
                ],
            ],
        );
    }
}

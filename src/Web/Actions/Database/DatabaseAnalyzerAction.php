<?php

namespace App\Web\Actions\Database;

use App\Database\Analyzer\DatabaseAnalyzer;
use App\Web\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;

class DatabaseAnalyzerAction extends Action
{

    /**
     * @inheritDoc
     */
    protected function action(): Response
    {
        $tables = DatabaseAnalyzer::getTables();
        $server = DatabaseAnalyzer::getServerInfo();
        $localVariables = DatabaseAnalyzer::getVariables(DatabaseAnalyzer::LOCAL_VARIABLES);
        $globalVariables = DatabaseAnalyzer::getVariables(DatabaseAnalyzer::GLOBAL_VARIABLES);
        $sessionVariables = DatabaseAnalyzer::getVariables(DatabaseAnalyzer::SESSION_VARIABLES);

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
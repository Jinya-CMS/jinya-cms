<?php

namespace App\Web\Actions\Database;

use App\Database\Analyzer\DatabaseAnalyzer;
use App\Database\Analyzer\VariablesType;
use App\Web\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;

/**
 *
 */
class DatabaseAnalyzerAction extends Action
{
    /**
     * @inheritDoc
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

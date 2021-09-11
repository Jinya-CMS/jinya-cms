<?php

namespace App\Web\Actions\Statistics;

use App\Statistics\MatomoClient;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/statistics/visits/browser', JinyaAction::GET)]
#[Authenticated(Authenticated::WRITER)]
class GetVisitsByBrowserAction extends Action
{

    /**
     * @throws \JsonException
     */
    protected function action(): Response
    {
        $client = MatomoClient::newClient();

        return $this->respond($client->getVisitsByBrowsers());
    }
}
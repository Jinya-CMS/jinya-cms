<?php

namespace App\Web\Actions\Statistics;

use App\Statistics\MatomoClient;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/statistics/visits/country', JinyaAction::GET)]
#[Authenticated(Authenticated::WRITER)]
class GetVisitsByCountryAction extends Action
{

    /**
     * @inheritDoc
     */
    protected function action(): Response
    {
        $matomoClient = MatomoClient::newClient();

        return $this->respond($matomoClient->getVisitsByCountry());
    }
}
<?php

namespace App\Web\Actions\Statistics;

use App\Statistics\MatomoClient;
use App\Web\Actions\Action;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

/**
 *
 */
class GetVisitsByCountryAction extends Action
{

    /**
     * @inheritDoc
     * @throws JsonException
     */
    protected function action(): Response
    {
        $matomoClient = MatomoClient::newClient();

        return $this->respond($matomoClient->getVisitsByCountry());
    }
}
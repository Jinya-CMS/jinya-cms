<?php

namespace App\Web\Actions\Statistics;

use App\Statistics\MatomoClient;
use App\Web\Actions\Action;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Action to get visits by country from matomo
 */
class GetVisitsByCountryAction extends Action
{

    /**
     * Gets the visits by country from matomo
     *
     * @throws JsonException
     */
    protected function action(): Response
    {
        $matomoClient = MatomoClient::newClient();

        return $this->respond($matomoClient->getVisitsByCountry());
    }
}
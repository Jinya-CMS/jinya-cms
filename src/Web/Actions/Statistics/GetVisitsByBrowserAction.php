<?php

namespace App\Web\Actions\Statistics;

use App\Statistics\MatomoClient;
use App\Web\Actions\Action;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Action to get visits by browser from matomo
 * @codeCoverageIgnore
 */
class GetVisitsByBrowserAction extends Action
{

    /**
     * Gets the visits by browser from matomo
     *
     * @throws JsonException
     */
    protected function action(): Response
    {
        $client = MatomoClient::newClient();

        return $this->respond($client->getVisitsByBrowsers());
    }
}
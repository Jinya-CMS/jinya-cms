<?php

namespace App\Web\Actions\Statistics;

use App\Statistics\MatomoClient;
use App\Web\Actions\Action;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Action to get visits by os version from matomo
 * @codeCoverageIgnore
 */
class GetVisitsByOsAction extends Action
{
    /**
     * Gets the visits by os versions from matomo
     *
     * @throws JsonException
     */
    protected function action(): Response
    {
        $client = MatomoClient::newClient();

        return $this->respond($client->getVisitsByOsVersions());
    }
}

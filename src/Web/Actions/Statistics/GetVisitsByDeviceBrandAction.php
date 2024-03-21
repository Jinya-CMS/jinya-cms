<?php

namespace App\Web\Actions\Statistics;

use App\Statistics\MatomoClient;
use App\Web\Actions\Action;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Action to get visits by device brand from matomo
 * @codeCoverageIgnore
 */
class GetVisitsByDeviceBrandAction extends Action
{
    /**
     * Gets the visits by device brand from matomo
     *
     * @throws JsonException
     */
    protected function action(): Response
    {
        $client = MatomoClient::newClient();

        return $this->respond($client->getVisitsByDeviceBrand());
    }
}

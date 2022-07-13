<?php

namespace App\Web\Actions\Statistics;

use App\Statistics\MatomoClient;
use App\Web\Actions\Action;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Action to get visits by referrer from matomo
 * @codeCoverageIgnore
 */
class GetVisitsByReferrerAction extends Action
{

    /**
     * Gets the visits by referrer from matomo
     *
     * @throws JsonException
     */
    protected function action(): Response
    {
        $client = MatomoClient::newClient();

        return $this->respond($client->getVisitsByReferrer());
    }
}
<?php

namespace App\Web\Actions\Statistics;

use App\OpenApiGeneration\Attributes\OpenApiRequest;
use App\OpenApiGeneration\Attributes\OpenApiResponse;
use App\Statistics\MatomoClient;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/statistics/visits/referrer', JinyaAction::GET)]
#[Authenticated(Authenticated::WRITER)]
#[OpenApiRequest('This action gets the visit statistics by referrer')]
#[OpenApiResponse('A successful response', example: MatomoClient::STATS_EXAMPLE, exampleName: 'Returned statistics', schema: MatomoClient::STATS_SCHEMA)]
class GetVisitsByReferrerAction extends Action
{

    /**
     * @throws \JsonException
     */
    protected function action(): Response
    {
        $client = MatomoClient::newClient();

        return $this->respond($client->getVisitsByReferrer());
    }
}
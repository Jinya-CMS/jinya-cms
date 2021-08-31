<?php

namespace App\Web\Actions\LocateIp;

use App\OpenApiGeneration\Attributes\OpenApiParameter;
use App\OpenApiGeneration\Attributes\OpenApiRequest;
use App\OpenApiGeneration\Attributes\OpenApiResponse;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/ip-location/{ip}', JinyaAction::GET)]
#[Authenticated]
#[OpenApiRequest('This action locates the given IP')]
#[OpenApiParameter('ip', true)]
#[OpenApiResponse('IP was located', example: [
    'country' => OpenApiResponse::FAKER_WORD,
    'region' => OpenApiResponse::FAKER_WORD,
    'city' => OpenApiResponse::FAKER_WORD,
], exampleName: 'Located IP', schema: [
    'country' => ['type' => 'string'],
    'region' => ['type' => 'string'],
    'city' => ['type' => 'string'],
])]
class LocatorAction extends Action
{
    /**
     * {@inheritDoc}
     * @throws JsonException
     * @throws GuzzleException
     */
    public function action(): Response
    {
        $ip = $this->args['ip'];
        $client = new Client();
        $result = $client->get("https://freegeoip.app/json/$ip");
        $location = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        return $this->respond(
            [
                'country' => $location['country_name'],
                'region' => $location['region_name'],
                'city' => $location['city'],
            ]
        );
    }
}

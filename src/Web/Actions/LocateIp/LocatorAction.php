<?php

namespace App\Web\Actions\LocateIp;

use App\Web\Actions\Action;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class LocatorAction extends Action
{
    /**
     * {@inheritDoc}
     * @throws JsonException
     * @throws GuzzleException
     */
    protected function action(): Response
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

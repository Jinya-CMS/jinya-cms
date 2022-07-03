<?php

namespace App\Web\Actions\LocateIp;

use App\Web\Actions\Action;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Action to locate an IP with freegeoip.app
 */
class LocatorAction extends Action
{
    /**
     * Locates the given IP
     *
     * @throws JsonException
     * @throws GuzzleException
     */
    protected function action(): Response
    {
        $ip = $this->args['ip'];
        $client = new Client();
        $result = $client->get("http://ip-api.com/json/$ip");
        $location = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        return $this->respond(
            [
                'country' => $location['country'],
                'region' => $location['regionName'],
                'city' => $location['city'],
            ]
        );
    }
}

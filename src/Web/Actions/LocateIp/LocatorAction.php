<?php

namespace App\Web\Actions\LocateIp;

use App\Web\Actions\Action;
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
     */
    protected function action(): Response
    {
        $ip = $this->args['ip'];
        /**
         * @phpstan-ignore-next-line
         */
        $location = json_decode(file_get_contents("https://ip.jinya.de/?ip=$ip"), true, 512, JSON_THROW_ON_ERROR);

        return $this->respond($location);
    }
}

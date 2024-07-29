<?php

namespace Jinya\Cms\Web\Controllers;

use Jinya\Cms\Locate\IpToLocationService;
use Jinya\Cms\Web\Middleware\AuthorizationMiddleware;
use Jinya\Router\Attributes\Controller;
use Jinya\Router\Attributes\HttpMethod;
use Jinya\Router\Attributes\Middlewares;
use Jinya\Router\Attributes\Route;
use JsonException;
use Psr\Http\Message\ResponseInterface;

#[Controller]
#[Middlewares(new AuthorizationMiddleware())]
class LocateIpController extends BaseController
{
    /**
     * Locates the given IP
     * @throws JsonException
     */
    #[Route(HttpMethod::GET, 'api/ip-location/{ip}')]
    public function locateIp(string $ip): ResponseInterface
    {
        $ipToLocationService = new IpToLocationService();
        $location = $ipToLocationService->locateIp($ip);
        $location['poweredBy'] = 'https://db-ip.com';

        return $this->json($location);
    }
}

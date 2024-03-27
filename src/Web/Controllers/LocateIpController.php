<?php

namespace App\Web\Controllers;

use App\Web\Middleware\AuthorizationMiddleware;
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
    #[Route(HttpMethod::GET, '/api/ip-location/{ip}')]
    public function locateIp(string $ip): ResponseInterface
    {
        /** @phpstan-ignore-next-line */
        $location = json_decode(file_get_contents("https://ip.jinya.de/?ip=$ip"), true, 512, JSON_THROW_ON_ERROR);

        return $this->json($location);
    }
}

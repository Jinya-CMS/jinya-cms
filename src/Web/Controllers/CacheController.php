<?php

namespace Jinya\Cms\Web\Controllers;

use Jinya\Cms\Utils\CacheUtils;
use Jinya\Cms\Web\Middleware\AuthorizationMiddleware;
use Jinya\Router\Attributes\Controller;
use Jinya\Router\Attributes\HttpMethod;
use Jinya\Router\Attributes\Middlewares;
use Jinya\Router\Attributes\Route;
use Psr\Http\Message\ResponseInterface;

#[Controller('api/cache')]
#[Middlewares(new AuthorizationMiddleware(ROLE_ADMIN))]
class CacheController extends BaseController
{
    #[Route(HttpMethod::DELETE)]
    public function clearCaches(): ResponseInterface
    {
        CacheUtils::clearDatabaseCache();
        CacheUtils::clearRouterCache();
        CacheUtils::clearOpcache();

        return $this->noContent();
    }

    #[Route(HttpMethod::PUT)]
    public function warmupCache(): ResponseInterface
    {
        CacheUtils::recreateRoutingCache();

        return $this->noContent();
    }
}

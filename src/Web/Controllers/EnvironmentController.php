<?php

namespace Jinya\Cms\Web\Controllers;

use Jinya\Cms\Web\Middleware\AuthorizationMiddleware;
use Jinya\Router\Attributes\Controller;
use Jinya\Router\Attributes\HttpMethod;
use Jinya\Router\Attributes\Middlewares;
use Jinya\Router\Attributes\Route;
use JsonException;
use Psr\Http\Message\ResponseInterface;

#[Controller]
#[Middlewares(new AuthorizationMiddleware(ROLE_ADMIN))]
class EnvironmentController extends BaseController
{
    /**
     * @throws JsonException
     */
    #[Route(HttpMethod::GET, 'api/environment')]
    public function getEnvironment(): ResponseInterface
    {
        $env = array_filter(
            $_ENV,
            static fn ($key) => str_starts_with($key, 'MAILER') || str_starts_with($key, 'JINYA') || str_starts_with(
                $key,
                'MYSQL'
            ),
            ARRAY_FILTER_USE_KEY
        );
        $data = array_map(
            static fn ($key, $value) => [
                'key' => $key,
                'value' => stripos($key, 'password') === false ? $value : '••••••',
            ],
            array_keys($env),
            array_values($env)
        );

        return $this->json($data);
    }
}

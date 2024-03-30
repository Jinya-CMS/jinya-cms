<?php

namespace Jinya\Cms\Web\Controllers;

use Jinya\Cms\Authentication\CurrentUser;
use Jinya\Cms\Database\ApiKey;
use Jinya\Cms\Web\Middleware\AuthorizationMiddleware;
use Jinya\Router\Attributes\Controller;
use Jinya\Router\Attributes\HttpMethod;
use Jinya\Router\Attributes\Middlewares;
use Jinya\Router\Attributes\Route;
use JsonException;
use Psr\Http\Message\ResponseInterface;

#[Controller]
#[Middlewares(new AuthorizationMiddleware())]
class ApiKeyController extends BaseController
{
    /**
     * @throws JsonException
     */
    #[Route(route: '/api/api-key')]
    public function getApiKeys(): ResponseInterface
    {
        $keys = ApiKey::findByArtist(CurrentUser::$currentUser->id);

        return $this->jsonIterator($keys);
    }

    /**
     * @throws JsonException
     */
    #[Route(HttpMethod::DELETE, '/api/api-key/{key}')]
    public function deleteApiKey(string $key): ResponseInterface
    {
        $apiKey = ApiKey::findByApiKey($key);
        if ($apiKey === null) {
            return $this->entityNotFound('Api key not found');
        }

        $apiKey->delete();

        return $this->noContent();
    }
}

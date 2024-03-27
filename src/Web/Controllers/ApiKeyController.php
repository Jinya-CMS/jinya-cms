<?php

namespace App\Web\Controllers;

use App\Authentication\CurrentUser;
use App\Database\ApiKey;
use App\Web\Middleware\AuthorizationMiddleware;
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

        return $this->jsonIteratorPlain($keys);
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

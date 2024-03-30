<?php

namespace Jinya\Cms\Web\Controllers;

use Jinya\Cms\Database\Exceptions\TransactionFailedException;
use Jinya\Cms\Database\ModernPage;
use Jinya\Cms\Web\Middleware\AuthorizationMiddleware;
use Jinya\Router\Attributes\Controller;
use Jinya\Router\Attributes\HttpMethod;
use Jinya\Router\Attributes\Middlewares;
use Jinya\Router\Attributes\Route;
use JsonException;
use Psr\Http\Message\ResponseInterface;

#[Controller]
class ModernPageController extends BaseController
{
    /**
     * @throws JsonException
     */
    #[Route(HttpMethod::GET, 'api/modern-page/{id}/section')]
    #[Middlewares(new AuthorizationMiddleware(ROLE_READER))]
    public function getSections(int $id): ResponseInterface
    {
        $page = ModernPage::findById($id);
        if ($page === null) {
            return $this->entityNotFound('Modern page not found');
        }

        $segments = $page->getSections();

        return $this->jsonIteratorPlain($segments);
    }

    /**
     * @throws TransactionFailedException
     * @throws JsonException
     */
    #[Route(HttpMethod::PUT, 'api/modern-page/{id}/section')]
    #[Middlewares(new AuthorizationMiddleware(ROLE_WRITER))]
    public function replaceSections(int $id): ResponseInterface
    {
        $page = ModernPage::findById($id);
        if ($page === null) {
            return $this->entityNotFound('Modern page not found');
        }

        /** @var array<int, mixed> $body */
        $body = $this->body;
        $page->replaceSections($body);

        return $this->noContent();
    }
}

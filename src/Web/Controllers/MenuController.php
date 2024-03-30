<?php

namespace Jinya\Cms\Web\Controllers;

use Jinya\Cms\Database\Exceptions\TransactionFailedException;
use Jinya\Cms\Database\Menu;
use Jinya\Cms\Web\Middleware\AuthorizationMiddleware;
use Jinya\Router\Attributes\Controller;
use Jinya\Router\Attributes\HttpMethod;
use Jinya\Router\Attributes\Middlewares;
use Jinya\Router\Attributes\Route;
use JsonException;
use Psr\Http\Message\ResponseInterface;

#[Controller]
class MenuController extends BaseController
{
    /**
     * @throws JsonException
     */
    #[Route(HttpMethod::GET, '/api/menu/{id}/item')]
    #[Middlewares(new AuthorizationMiddleware(ROLE_READER))]
    public function getItems(int $id): ResponseInterface
    {
        $menu = Menu::findById($id);
        if ($menu === null) {
            return $this->entityNotFound('Menu not found');
        }

        $items = $menu->getItems();

        return $this->jsonIteratorPlain($items);
    }

    /**
     * @throws TransactionFailedException
     * @throws JsonException
     */
    #[Route(HttpMethod::PUT, '/api/menu/{id}/item')]
    #[Middlewares(new AuthorizationMiddleware(ROLE_WRITER))]
    public function replaceItems(int $id): ResponseInterface
    {
        $menu = Menu::findById($id);
        if ($menu === null) {
            return $this->entityNotFound('Menu not found');
        }

        $menu->replaceItems($this->body);

        return $this->noContent();
    }
}

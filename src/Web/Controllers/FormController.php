<?php

namespace Jinya\Cms\Web\Controllers;

use Jinya\Cms\Database\Exceptions\TransactionFailedException;
use Jinya\Cms\Database\Form;
use Jinya\Cms\Web\Middleware\AuthorizationMiddleware;
use Jinya\Router\Attributes\Controller;
use Jinya\Router\Attributes\HttpMethod;
use Jinya\Router\Attributes\Middlewares;
use Jinya\Router\Attributes\Route;
use JsonException;
use Psr\Http\Message\ResponseInterface;

#[Controller]
class FormController extends BaseController
{
    /**
     * @throws JsonException
     */
    #[Route(HttpMethod::GET, '/api/form/{id}/item')]
    #[Middlewares(new AuthorizationMiddleware(ROLE_READER))]
    public function getItems(int $id): ResponseInterface
    {
        $form = Form::findById($id);
        if ($form === null) {
            return $this->entityNotFound('Form not found');
        }

        $items = $form->getItems();

        return $this->jsonIteratorPlain($items);
    }

    /**
     * @throws TransactionFailedException
     * @throws JsonException
     */
    #[Route(HttpMethod::PUT, '/api/form/{id}/item')]
    #[Middlewares(new AuthorizationMiddleware(ROLE_WRITER))]
    public function replaceItems(int $id): ResponseInterface
    {
        $form = Form::findById($id);
        if ($form === null) {
            return $this->entityNotFound('Form not found');
        }

        $form->replaceItems($this->body);

        return $this->noContent();
    }
}

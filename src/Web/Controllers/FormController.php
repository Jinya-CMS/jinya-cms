<?php

namespace App\Web\Controllers;

use App\Database\Exceptions\TransactionFailedException;
use App\Database\Form;
use App\Web\Middleware\AuthorizationMiddleware;
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

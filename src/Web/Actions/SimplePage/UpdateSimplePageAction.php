<?php

namespace App\Web\Actions\SimplePage;

use App\Database\Exceptions\UniqueFailedException;
use App\Database\SimplePage;
use App\Web\Actions\Action;
use App\Web\Exceptions\ConflictException;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class UpdateSimplePageAction extends Action
{

    /**
     * @inheritDoc
     * @return Response
     * @throws ConflictException
     * @throws JsonException
     * @throws NoResultException
     * @throws UniqueFailedException
     * @throws \App\Database\Exceptions\ForeignKeyFailedException
     * @throws \App\Database\Exceptions\InvalidQueryException
     */
    protected function action(): Response
    {
        $body = $this->request->getParsedBody();
        $id = $this->args['id'];
        $page = SimplePage::findById($id);
        if ($page === null) {
            throw new NoResultException($this->request, 'Page not found');
        }

        if (isset($body['title'])) {
            $page->title = $body['title'];
        }

        if (isset($body['content'])) {
            $page->content = $body['content'];
        }

        try {
            $page->update();
        } catch (UniqueFailedException $exception) {
            throw new ConflictException($this->request, 'Title already used');
        }

        return $this->noContent();
    }
}
<?php

namespace App\Web\Actions\SimplePage;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\SimplePage;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\ConflictException;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/page/{id}', JinyaAction::PUT)]
#[Authenticated(Authenticated::WRITER)]
class UpdateSimplePageAction extends Action
{

    /**
     * @inheritDoc
     * @return Response
     * @throws ConflictException
     * @throws JsonException
     * @throws NoResultException
     * @throws UniqueFailedException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
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
        } catch (UniqueFailedException) {
            throw new ConflictException($this->request, 'Title already used');
        }

        return $this->noContent();
    }
}
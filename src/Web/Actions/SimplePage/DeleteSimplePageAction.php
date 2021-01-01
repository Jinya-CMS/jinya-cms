<?php

namespace App\Web\Actions\SimplePage;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\SimplePage;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/page/{id}', JinyaAction::DELETE)]
#[Authenticated(Authenticated::WRITER)]
class DeleteSimplePageAction extends Action
{

    /**
     * @inheritDoc
     * @return Response
     * @throws JsonException
     * @throws NoResultException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    protected function action(): Response
    {
        $page = SimplePage::findById($this->args['id']);
        if ($page === null) {
            throw new NoResultException($this->request, 'Page not found');
        }

        $page->delete();

        return $this->noContent();
    }
}
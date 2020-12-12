<?php

namespace App\Web\Actions\SimplePage;

use App\Database\SimplePage;
use App\Web\Actions\Action;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class DeleteSimplePageAction extends Action
{

    /**
     * @inheritDoc
     * @return Response
     * @throws JsonException
     * @throws NoResultException
     * @throws \App\Database\Exceptions\ForeignKeyFailedException
     * @throws \App\Database\Exceptions\InvalidQueryException
     * @throws \App\Database\Exceptions\UniqueFailedException
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
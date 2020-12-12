<?php

namespace App\Web\Actions\SimplePage;

use App\Database\SimplePage;
use App\Web\Actions\Action;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class GetSimplePageBySlugAction extends Action
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
        $id = $this->args['id'];
        $page = SimplePage::findById($id);
        if ($page === null) {
            throw new NoResultException($this->request, 'Page not found');
        }

        return $this->respond($page->format());
    }
}
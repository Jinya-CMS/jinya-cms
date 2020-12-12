<?php

namespace App\Web\Actions\Gallery;

use App\Database\Gallery;
use App\Web\Actions\Action;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class DeleteGalleryAction extends Action
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
        $gallery = Gallery::findById($this->args['id']);
        if ($gallery === null) {
            throw new NoResultException($this->request, 'Gallery not found');
        }

        $gallery->delete();

        return $this->noContent();
    }
}
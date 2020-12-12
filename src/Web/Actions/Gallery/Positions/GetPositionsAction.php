<?php

namespace App\Web\Actions\Gallery\Positions;

use App\Database\Gallery;
use App\Web\Actions\Action;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class GetPositionsAction extends Action
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
        $galleryId = $this->args['galleryId'];
        $gallery = Gallery::findById($galleryId);
        if (!$gallery) {
            throw new NoResultException($this->request, 'Gallery not found');
        }

        return $this->respond($this->formatIterator($gallery->getFiles()));
    }
}
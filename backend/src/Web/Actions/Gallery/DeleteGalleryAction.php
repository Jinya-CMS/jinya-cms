<?php

namespace App\Web\Actions\Gallery;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\Gallery;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/media/gallery/{id}', JinyaAction::DELETE)]
#[Authenticated(role: Authenticated::WRITER)]
class DeleteGalleryAction extends Action
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
        $gallery = Gallery::findById($this->args['id']);
        if ($gallery === null) {
            throw new NoResultException($this->request, 'Gallery not found');
        }

        $gallery->delete();

        return $this->noContent();
    }
}
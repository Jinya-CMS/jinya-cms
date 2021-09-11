<?php

namespace App\Web\Actions\Gallery\Positions;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\Gallery;
use App\Database\GalleryFilePosition;
use App\OpenApiGeneration\Attributes\OpenApiArrayResponse;
use App\OpenApiGeneration\Attributes\OpenApiParameter;
use App\OpenApiGeneration\Attributes\OpenApiRequest;
use App\OpenApiGeneration\Attributes\OpenApiResponse;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\NoResultException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/media/gallery/{galleryId}/file', JinyaAction::GET)]
#[Authenticated(role: Authenticated::READER)]
class GetPositionsAction extends Action
{
    /**
     * {@inheritDoc}
     * @throws NoResultException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
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

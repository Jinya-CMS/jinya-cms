<?php

namespace App\Web\Actions\Gallery\Positions;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\File;
use App\Database\Gallery;
use App\Database\GalleryFilePosition;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Attributes\RequiredFields;
use App\Web\Exceptions\NoResultException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/media/gallery/{galleryId}/file', JinyaAction::POST)]
#[Authenticated(role: Authenticated::WRITER)]
#[RequiredFields(['file', 'position'])]
class CreatePositionAction extends Action
{
    /**
     * {@inheritDoc}
     * @return Response
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     * @throws UniqueFailedException
     */
    protected function action(): Response
    {
        $body = $this->request->getParsedBody();
        $galleryId = $this->args['galleryId'];
        $position = $body['position'];
        $file = $body['file'];

        if (!File::findById($file)) {
            throw new NoResultException($this->request, 'File not found');
        }

        if (!Gallery::findById($galleryId)) {
            throw new NoResultException($this->request, 'Gallery not found');
        }

        $galleryFilePosition = new GalleryFilePosition();
        $galleryFilePosition->fileId = $file;
        $galleryFilePosition->galleryId = $galleryId;
        $galleryFilePosition->position = $position;

        $galleryFilePosition->create();

        return $this->respond($galleryFilePosition->format(), Action::HTTP_CREATED);
    }
}

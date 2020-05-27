<?php

namespace App\Web\Actions\Gallery\Positions;

use App\Database\Exceptions\UniqueFailedException;
use App\Database\File;
use App\Database\Gallery;
use App\Database\GalleryFilePosition;
use App\Web\Actions\Action;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpNotFoundException;

class CreatePositionAction extends Action
{

    /**
     * @inheritDoc
     * @return Response
     * @throws HttpNotFoundException
     * @throws UniqueFailedException
     * @throws JsonException
     */
    protected function action(): Response
    {
        $body = $this->request->getParsedBody();
        $galleryId = $this->args['galleryId'];
        $position = $body['position'];
        $file = $body['file'];

        if (!File::findById($file)) {
            throw new HttpNotFoundException($this->request, 'File not found');
        }

        if (!Gallery::findById($galleryId)) {
            throw new HttpNotFoundException($this->request, 'Gallery not found');
        }

        $galleryFilePosition = new GalleryFilePosition();
        $galleryFilePosition->fileId = $file;
        $galleryFilePosition->galleryId = $galleryId;
        $galleryFilePosition->position = $position;

        $galleryFilePosition->create();

        return $this->respond($galleryFilePosition->format(), Action::HTTP_CREATED);
    }
}
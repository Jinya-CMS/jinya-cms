<?php

namespace App\Web\Actions\Gallery\Positions;

use App\Database\Exceptions\UniqueFailedException;
use App\Database\File;
use App\Database\GalleryFilePosition;
use App\Web\Actions\Action;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class UpdatePositionAction extends Action
{

    /**
     * @inheritDoc
     * @return Response
     * @throws JsonException
     * @throws NoResultException
     * @throws UniqueFailedException
     * @throws \App\Database\Exceptions\ForeignKeyFailedException
     * @throws \App\Database\Exceptions\InvalidQueryException
     */
    protected function action(): Response
    {
        $galleryId = $this->args['galleryId'];
        $position = $this->args['position'];
        $galleryFilePosition = GalleryFilePosition::findByPosition($galleryId, $position);

        if (!$galleryFilePosition) {
            throw new NoResultException($this->request, 'Position not found');
        }

        $body = $this->request->getParsedBody();
        $fileId = $body['file'];

        if ($fileId) {
            $file = File::findById($fileId);
            if (!$file) {
                throw new NoResultException($this->request, 'File not found');
            }

            $galleryFilePosition->fileId = $fileId;
        }

        if (isset($body['newPosition'])) {
            $galleryFilePosition->move($body['newPosition']);
        }

        return $this->noContent();
    }
}
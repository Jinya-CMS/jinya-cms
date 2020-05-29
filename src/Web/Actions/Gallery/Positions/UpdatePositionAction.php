<?php

namespace App\Web\Actions\Gallery\Positions;

use App\Database\Exceptions\UniqueFailedException;
use App\Database\File;
use App\Database\GalleryFilePosition;
use App\Web\Actions\Action;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpNotFoundException;

class UpdatePositionAction extends Action
{

    /**
     * @inheritDoc
     * @throws HttpNotFoundException
     * @throws JsonException
     * @throws UniqueFailedException
     */
    protected function action(): Response
    {
        $galleryId = $this->args['galleryId'];
        $position = $this->args['position'];
        $galleryFilePosition = GalleryFilePosition::findByPosition($galleryId, $position);

        if (!$galleryFilePosition) {
            throw new HttpNotFoundException($this->request, 'Position not found');
        }

        $body = $this->request->getParsedBody();
        $fileId = $body['file'];

        $file = File::findById($fileId);
        if (!$file) {
            throw new HttpNotFoundException($this->request, 'File not found');
        }

        $galleryFilePosition->fileId = $fileId;
        if ($body['position']) {
            $newPosition = $body['position'];
            $galleryFilePosition->move($newPosition);
        }

        return $this->noContent();
    }
}
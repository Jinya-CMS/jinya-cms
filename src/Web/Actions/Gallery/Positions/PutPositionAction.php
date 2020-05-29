<?php

namespace App\Web\Actions\Gallery\Positions;

use App\Database\Exceptions\UniqueFailedException;
use App\Database\File;
use App\Database\GalleryFilePosition;
use App\Web\Actions\Action;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpNotFoundException;

class PutPositionAction extends Action
{

    /**
     * @inheritDoc
     * @throws HttpNotFoundException
     * @throws JsonException
     * @throws UniqueFailedException
     */
    protected function action(): Response
    {
        $galleryFileId = $this->args['galleryFileId'];
        $galleryFile = GalleryFilePosition::findById($galleryFileId);

        if (!$galleryFile) {
            throw new HttpNotFoundException($this->request, 'Position not found');
        }

        $body = $this->request->getParsedBody();
        $fileId = $body['file'];

        $file = File::findById($fileId);
        if (!$file) {
            throw new HttpNotFoundException($this->request, 'File not found');
        }

        $galleryFile->fileId = $fileId;
        if ($body['position']) {
            $newPosition = $body['position'];
            $galleryFile->move($newPosition);
        }

        return $this->noContent();
    }
}
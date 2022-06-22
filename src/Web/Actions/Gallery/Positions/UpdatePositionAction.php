<?php

namespace App\Web\Actions\Gallery\Positions;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\File;
use App\Database\GalleryFilePosition;
use App\Web\Actions\Action;
use App\Web\Exceptions\NoResultException;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Action to update a position
 */
class UpdatePositionAction extends Action
{
    /**
     * Updates the given gallery file position by gallery id and position
     *
     * @return Response
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     * @throws UniqueFailedException
     * @throws \Jinya\PDOx\Exceptions\NoResultException
     */
    protected function action(): Response
    {
        $galleryId = $this->args['galleryId'];
        $position = $this->args['position'];
        $galleryFilePosition = GalleryFilePosition::findByPosition($galleryId, $position);

        if (!$galleryFilePosition) {
            throw new NoResultException($this->request, 'Position not found');
        }


        $fileId = $this->body['file'] ?? null;

        if ($fileId) {
            $file = File::findById($fileId);
            if (!$file) {
                throw new NoResultException($this->request, 'File not found');
            }

            $galleryFilePosition->fileId = $fileId;
        }

        if (isset($this->body['newPosition'])) {
            $galleryFilePosition->move($this->body['newPosition']);
        }

        return $this->noContent();
    }
}

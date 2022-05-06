<?php

namespace App\Web\Actions\Gallery\Positions;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\GalleryFilePosition;
use App\Web\Actions\Action;
use App\Web\Exceptions\NoResultException;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Psr\Http\Message\ResponseInterface as Response;

/**
 *
 */
class DeletePositionAction extends Action
{
    /**
     * {@inheritDoc}
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
            throw new NoResultException($this->request, 'Gallery file position not found');
        }
        $galleryFilePosition->delete();

        return $this->noContent();
    }
}

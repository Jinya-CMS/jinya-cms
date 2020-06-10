<?php

namespace App\Web\Actions\Gallery\Positions;

use App\Database\GalleryFilePosition;
use App\Web\Actions\Action;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpNotFoundException;

class DeletePositionAction extends Action
{

    /**
     * @inheritDoc
     * @return Response
     * @throws JsonException
     * @throws NoResultException
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
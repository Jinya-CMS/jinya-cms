<?php

namespace App\Web\Actions\Gallery\Positions;

use App\Database\GalleryFilePosition;
use App\Web\Actions\Action;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class DeletePositionAction extends Action
{

    /**
     * @inheritDoc
     * @throws JsonException
     */
    protected function action(): Response
    {
        $positionId = $this->args['galleryFileId'];
        $galleryFilePosition = GalleryFilePosition::findById($positionId);
        $galleryFilePosition->delete();

        return $this->noContent();
    }
}
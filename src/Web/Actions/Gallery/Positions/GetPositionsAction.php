<?php

namespace App\Web\Actions\Gallery\Positions;

use App\Database\Gallery;
use App\Web\Actions\Action;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpNotFoundException;

class GetPositionsAction extends Action
{

    /**
     * @inheritDoc
     * @throws JsonException
     */
    protected function action(): Response
    {
        $galleryId = $this->args['galleryId'];
        $gallery = Gallery::findById($galleryId);
        if (!$gallery) {
            throw new HttpNotFoundException($this->request, 'Gallery not found');
        }

        return $this->respond(iterator_to_array($gallery->getFiles()));
    }
}
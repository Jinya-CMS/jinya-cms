<?php

namespace App\Web\Actions\Gallery;

use App\Database\Gallery;
use App\Web\Actions\Action;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class GetGalleryBySlugAction extends Action
{

    /**
     * @inheritDoc
     * @throws JsonException
     * @throws NoResultException
     */
    protected function action(): Response
    {
        $slug = $this->args['slug'];
        $gallery = Gallery::findBySlug($slug);
        if ($gallery === null) {
            throw new NoResultException($this->request, 'Gallery not found');
        }

        return $this->respond($gallery->format());
    }
}